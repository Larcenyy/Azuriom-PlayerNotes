<?php

namespace Azuriom\Plugin\PlayerNotes\Tests;

use Azuriom\Models\User;
use Azuriom\Plugin\PlayerNotes\Models\Note;
use Azuriom\Plugin\PlayerNotes\Models\Tag;
use Illuminate\Support\Facades\DB;

class PlayerNotesTest extends TestCase
{
    private function createNote(User $player, array $overrides = []): Note
    {
        $this->post(route('player-notes.admin.notes.store', $player), [
            'title' => 'Payment already refunded', 'content' => 'Refund reference 123.', ...$overrides,
        ])->assertRedirect(route('player-notes.admin.players.show', $player));

        return Note::latest('id')->firstOrFail();
    }

    public function test_guests_and_unauthorized_users_cannot_access_any_private_endpoint(): void
    {
        $this->get(route('player-notes.admin.index'))->assertRedirect(route('login'));
        $player = $this->player();
        $this->actingAs($this->staff());

        foreach (['index', 'players.index', 'tags.index'] as $name) {
            $this->get(route('player-notes.admin.'.$name))->assertForbidden();
        }

        $this->get(route('player-notes.admin.players.show', $player))->assertForbidden();
        $this->post(route('player-notes.admin.notes.store', $player), ['title' => 'Secret', 'content' => 'Hidden'])->assertForbidden();
        $this->assertDatabaseCount('player_notes', 0);
    }

    public function test_creation_tracks_the_real_author_and_saves_an_immutable_snapshot(): void
    {
        $staff = $this->staff(['player-notes.view', 'player-notes.create']);
        $this->actingAs($staff);
        $tag = Tag::create(['name' => 'Billing', 'color' => 'warning']);
        $note = $this->createNote($this->player(), ['tags' => [$tag->id], 'author_id' => 999, 'is_pinned' => 1]);

        $this->assertSame($staff->id, $note->author_id);
        $this->assertFalse($note->is_pinned);
        $this->assertSame(1, $note->version);
        $this->assertSame(['Billing'], $note->revisions()->first()->tags);
        $this->assertSame([$tag->id], $note->tags->modelKeys());
    }

    public function test_view_permission_alone_cannot_write_notes_or_tags(): void
    {
        $this->actingAs($this->staff(['player-notes.view']));
        $this->get(route('player-notes.admin.index'))->assertOk();
        $this->post(route('player-notes.admin.notes.store', $this->player()), ['title' => 'Test', 'content' => 'Test'])->assertForbidden();
        $this->post(route('player-notes.admin.tags.store'), ['name' => 'Test', 'color' => 'primary'])->assertForbidden();
    }

    public function test_validation_rejects_empty_oversized_and_invalid_note_data(): void
    {
        $this->actingAs($this->staff(['player-notes.view', 'player-notes.create']));
        $url = route('player-notes.admin.notes.store', $this->player());
        $this->post($url, ['title' => ' ', 'content' => ' '])->assertSessionHasErrors(['title', 'content']);
        $this->post($url, ['title' => str_repeat('a', 151), 'content' => str_repeat('b', 10001), 'tags' => [999]])
            ->assertSessionHasErrors(['title', 'content', 'tags.0']);
        $this->post($url, ['title' => 'Okay', 'content' => 'Okay', 'tags' => array_fill(0, 11, 1)])
            ->assertSessionHasErrors('tags');
        $this->assertDatabaseCount('player_notes', 0);
    }

    public function test_owners_can_edit_and_remove_all_tags_without_changing_the_author(): void
    {
        $staff = $this->staff(['player-notes.view', 'player-notes.create', 'player-notes.edit']);
        $this->actingAs($staff);
        $player = $this->player();
        $tag = Tag::create(['name' => 'Support', 'color' => 'info']);
        $note = $this->createNote($player, ['tags' => [$tag->id]]);
        $this->get(route('player-notes.admin.notes.edit', [$player, $note]))->assertOk();
        $this->put(route('player-notes.admin.notes.update', [$player, $note]), [
            'title' => 'Resolved', 'content' => 'Fixed today.', 'version' => 1, 'author_id' => 999,
        ])->assertRedirect();

        $note->refresh();
        $this->assertSame('Resolved', $note->title);
        $this->assertSame($staff->id, $note->author_id);
        $this->assertSame(2, $note->version);
        $this->assertCount(0, $note->tags);
        $this->assertSame(['Support'], $note->revisions()->oldest('id')->first()->tags);
        $this->assertCount(2, $note->revisions);
    }

    public function test_edit_and_delete_permissions_do_not_grant_access_to_other_authors_notes(): void
    {
        $this->actingAs($this->staff(['player-notes.view', 'player-notes.create']));
        $player = $this->player();
        $note = $this->createNote($player);
        $this->actingAs($this->staff(['player-notes.view', 'player-notes.edit', 'player-notes.delete']));
        $this->get(route('player-notes.admin.notes.edit', [$player, $note]))->assertForbidden();
        $this->put(route('player-notes.admin.notes.update', [$player, $note]), ['title' => 'Changed', 'content' => 'Changed', 'version' => 1])->assertForbidden();
        $this->delete(route('player-notes.admin.notes.destroy', [$player, $note]), ['version' => 1])->assertForbidden();
        $this->assertDatabaseCount('player_notes', 1);
    }

    public function test_moderators_require_the_corresponding_edit_or_delete_permission(): void
    {
        $this->actingAs($this->staff(['player-notes.view', 'player-notes.create']));
        $player = $this->player();
        $note = $this->createNote($player);
        $this->actingAs($this->staff(['player-notes.view', 'player-notes.moderate']));
        $this->get(route('player-notes.admin.notes.edit', [$player, $note]))->assertForbidden();
        $this->actingAs($this->staff(['player-notes.view', 'player-notes.moderate', 'player-notes.edit', 'player-notes.delete']));
        $this->put(route('player-notes.admin.notes.update', [$player, $note]), ['title' => 'Reviewed', 'content' => 'Reviewed', 'version' => 1])->assertRedirect();
        $this->delete(route('player-notes.admin.notes.destroy', [$player, $note]), ['version' => 2])->assertRedirect();
        $this->assertDatabaseCount('player_notes', 0);
        $this->assertDatabaseCount('player_notes_revisions', 0);
    }

    public function test_stale_edits_pins_and_deletions_cannot_overwrite_newer_changes(): void
    {
        $this->actingAs($this->staff([], true));
        $player = $this->player();
        $note = $this->createNote($player);
        $this->put(route('player-notes.admin.notes.update', [$player, $note]), ['title' => 'New', 'content' => 'New', 'version' => 1])->assertRedirect();
        $this->put(route('player-notes.admin.notes.update', [$player, $note]), ['title' => 'Old', 'content' => 'Old', 'version' => 1])->assertSessionHasErrors('version');
        $this->patch(route('player-notes.admin.notes.pin', [$player, $note]), ['is_pinned' => 1, 'version' => 1])->assertSessionHasErrors('version');
        $this->delete(route('player-notes.admin.notes.destroy', [$player, $note]), ['version' => 1])->assertSessionHasErrors('version');
        $this->assertSame('New', $note->fresh()->content);
        $this->assertDatabaseCount('player_notes_revisions', 2);
    }

    public function test_pin_permission_is_enforced_and_pinned_notes_appear_first(): void
    {
        $this->actingAs($this->staff(['player-notes.view', 'player-notes.create']));
        $player = $this->player();
        $first = $this->createNote($player, ['title' => 'First note']);
        $second = $this->createNote($player, ['title' => 'Second note']);
        $url = route('player-notes.admin.notes.pin', [$player, $first]);
        $this->patch($url, ['is_pinned' => 1, 'version' => 1])->assertForbidden();
        $this->actingAs($this->staff(['player-notes.view', 'player-notes.pin']));
        $this->patch($url, ['is_pinned' => 1, 'version' => 1])->assertRedirect();
        $this->get(route('player-notes.admin.index'))->assertOk()->assertSeeInOrder(['First note', 'Second note']);
        $this->get(route('player-notes.admin.index', ['pinned' => 1]))->assertOk()->assertDontSee('Second note');
        $this->assertSame('pinned', $first->revisions()->latest('version')->first()->action);
        $this->assertFalse($second->fresh()->is_pinned);
    }

    public function test_cross_player_note_urls_are_rejected(): void
    {
        $this->actingAs($this->staff([], true));
        $note = $this->createNote($this->player());
        $other = $this->player();
        $this->get(route('player-notes.admin.notes.edit', [$other, $note]))->assertNotFound();
        $this->get(route('player-notes.admin.notes.history', [$other, $note]))->assertNotFound();
        $this->put(route('player-notes.admin.notes.update', [$other, $note]), [])->assertNotFound();
        $this->delete(route('player-notes.admin.notes.destroy', [$other, $note]), [])->assertNotFound();
        $this->patch(route('player-notes.admin.notes.pin', [$other, $note]), [])->assertNotFound();
    }

    public function test_html_is_escaped_in_notes_titles_tags_and_history(): void
    {
        $this->actingAs($this->staff([], true));
        $player = $this->player();
        $tag = Tag::create(['name' => '<script>tag()</script>', 'color' => 'secondary']);
        $note = $this->createNote($player, [
            'title' => '<script>title()</script>', 'content' => '<img src=x onerror=alert(1)>', 'tags' => [$tag->id],
        ]);

        foreach ([route('player-notes.admin.index'), route('player-notes.admin.notes.history', [$player, $note])] as $url) {
            $this->get($url)->assertOk()->assertDontSee('<script>title()', false)
                ->assertDontSee('<img src=x', false)->assertDontSee('<script>tag()', false)
                ->assertSee('&lt;img src=x onerror=alert(1)&gt;', false);
        }
    }

    public function test_search_and_combined_filters_do_not_leak_other_players_notes(): void
    {
        $staff = $this->staff([], true);
        $this->actingAs($staff);
        $player = $this->player();
        $tag = Tag::create(['name' => 'Billing', 'color' => 'primary']);
        $this->createNote($player, ['title' => 'Refund 100%_!', 'tags' => [$tag->id]]);
        $this->createNote($this->player(), ['title' => 'Other player']);
        $this->get(route('player-notes.admin.index', ['search' => '%_!', 'tag' => $tag->id, 'author' => $staff->id]))
            ->assertOk()->assertSee('Refund 100%_!')->assertDontSee('Other player');
        $this->get(route('player-notes.admin.players.show', $player))->assertOk()->assertDontSee('Other player');
        $this->get(route('player-notes.admin.players.index', ['search' => (string) $player->id]))->assertOk()->assertSee($player->name);
    }

    public function test_tags_are_validated_and_deleting_one_preserves_notes_and_history(): void
    {
        $this->actingAs($this->staff([], true));
        $url = route('player-notes.admin.tags.store');
        $this->post($url, ['name' => 'Billing', 'color' => 'warning'])->assertRedirect();
        $this->post($url, ['name' => 'Billing', 'color' => 'invalid'])->assertSessionHasErrors(['name', 'color']);
        $tag = Tag::firstOrFail();
        $note = $this->createNote($this->player(), ['tags' => [$tag->id]]);
        $this->get(route('player-notes.admin.tags.index'))->assertOk();
        $this->get(route('player-notes.admin.tags.edit', $tag))->assertOk();
        $this->put(route('player-notes.admin.tags.update', $tag), ['name' => 'Payments', 'color' => 'success'])->assertRedirect();
        $this->delete(route('player-notes.admin.tags.destroy', $tag))->assertRedirect();
        $this->assertCount(0, $note->fresh()->tags);
        $this->assertSame(['Billing'], $note->revisions()->first()->tags);
        $this->assertDatabaseCount('player_notes', 1);
    }

    public function test_deleted_players_remain_readable_but_cannot_receive_new_notes(): void
    {
        $this->actingAs($this->staff([], true));
        $player = $this->player();
        $this->createNote($player);
        DB::table('users')->where('id', $player->id)->update(['deleted_at' => now()]);
        $this->get(route('player-notes.admin.players.show', $player))->assertOk()->assertSee('Payment already refunded');
        $this->post(route('player-notes.admin.notes.store', $player), ['title' => 'Test', 'content' => 'Test'])->assertNotFound();
    }

    public function test_translation_keys_match_between_english_and_french(): void
    {
        $en = require dirname(__DIR__, 2).'/resources/lang/en/admin.php';
        $fr = require dirname(__DIR__, 2).'/resources/lang/fr/admin.php';
        $this->assertSame(array_keys($en), array_keys($fr));

        foreach (['permissions', 'colors', 'events'] as $key) {
            $this->assertSame(array_keys($en[$key]), array_keys($fr[$key]));
        }
    }

    public function test_private_notes_are_not_available_without_admin_access_even_with_note_permissions(): void
    {
        $player = $this->player();
        $player->role->syncPermissions(['player-notes.view', 'player-notes.create']);
        $this->actingAs($player);
        $this->get(route('player-notes.admin.index'))->assertForbidden();
        $this->get(route('player-notes.admin.players.index'))->assertForbidden();
    }

    public function test_history_and_every_note_operation_require_view_permission(): void
    {
        $this->actingAs($this->staff([], true));
        $player = $this->player();
        $note = $this->createNote($player);
        $this->actingAs($this->staff(['player-notes.edit', 'player-notes.delete', 'player-notes.pin', 'player-notes.moderate']));
        $this->get(route('player-notes.admin.notes.edit', [$player, $note]))->assertForbidden();
        $this->get(route('player-notes.admin.notes.history', [$player, $note]))->assertForbidden();
        $this->put(route('player-notes.admin.notes.update', [$player, $note]), [])->assertForbidden();
        $this->patch(route('player-notes.admin.notes.pin', [$player, $note]), [])->assertForbidden();
        $this->delete(route('player-notes.admin.notes.destroy', [$player, $note]), [])->assertForbidden();
    }

    public function test_user_edit_card_respects_permissions_without_overriding_the_core_view(): void
    {
        $player = $this->player();
        $this->actingAs($this->staff(['admin.users']));
        $this->get(route('admin.users.edit', $player))->assertOk()
            ->assertDontSee(route('player-notes.admin.players.show', $player), false);

        $this->actingAs($this->staff(['admin.users', 'player-notes.view']));
        $this->get(route('admin.users.edit', $player))->assertOk()
            ->assertSee(route('player-notes.admin.players.show', $player), false);
    }

    public function test_pages_render_in_french_and_private_responses_are_not_cacheable(): void
    {
        $this->app->setLocale('fr');
        $this->actingAs($this->staff([], true));
        $this->get(route('player-notes.admin.index'))->assertOk()
            ->assertSee('Toutes les notes')->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->get(route('player-notes.admin.players.index'))->assertOk()->assertSee('Joueurs');
        $this->get(route('player-notes.admin.tags.index'))->assertOk()->assertSee('Étiquettes');
    }

    public function test_deleting_a_note_cascades_to_its_tags_and_revisions(): void
    {
        $this->actingAs($this->staff(['player-notes.view', 'player-notes.create', 'player-notes.delete']));
        $player = $this->player();
        $tag = Tag::create(['name' => 'Support', 'color' => 'info']);
        $note = $this->createNote($player, ['tags' => [$tag->id]]);
        $this->delete(route('player-notes.admin.notes.destroy', [$player, $note]), ['version' => 1])->assertRedirect();
        $this->assertDatabaseCount('player_notes', 0);
        $this->assertDatabaseCount('player_notes_revisions', 0);
        $this->assertDatabaseCount('player_notes_note_tag', 0);
        $this->assertDatabaseCount('player_notes_tags', 1);
    }

    public function test_pagination_keeps_filters_and_does_not_repeat_pinned_notes(): void
    {
        $this->actingAs($this->staff([], true));
        $player = $this->player();

        for ($i = 1; $i <= 17; $i++) {
            $this->createNote($player, ['title' => sprintf('Paginated note %02d', $i)]);
        }

        $this->get(route('player-notes.admin.index', ['search' => 'Paginated']))
            ->assertOk()->assertSee('Paginated note 17')->assertDontSee('Paginated note 01')
            ->assertSee('search=Paginated', false);
        $this->get(route('player-notes.admin.index', ['search' => 'Paginated', 'page' => 2]))
            ->assertOk()->assertSee('Paginated note 01')->assertDontSee('Paginated note 17');
    }

    public function test_plugin_migration_can_be_rolled_back_and_reapplied(): void
    {
        $migration = require dirname(__DIR__, 2).'/database/migrations/2026_10_16_000001_create_player_notes_tables.php';
        $migration->down();
        $this->assertFalse(DB::getSchemaBuilder()->hasTable('player_notes'));
        $migration->up();
        $this->assertDatabaseCount('player_notes', 0);
        $this->assertDatabaseCount('player_notes_revisions', 0);
    }
}
