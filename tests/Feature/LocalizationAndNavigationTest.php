<?php

namespace Azuriom\Plugin\PlayerNotes\Tests;

use Azuriom\Models\NavbarElement;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\DataProvider;

class LocalizationAndNavigationTest extends TestCase
{
    public static function locales(): array
    {
        return [
            'English' => ['en', 'All notes'],
            'French' => ['fr', 'Toutes les notes'],
            'German' => ['de', 'Alle Notizen'],
            'Spanish' => ['es_ES', 'Todas las notas'],
            'Brazilian Portuguese' => ['pt_BR', 'Todas as notas'],
            'Dutch' => ['nl', 'Alle notities'],
            'Polish' => ['pl', 'Wszystkie notatki'],
            'Russian' => ['ru', 'Все заметки'],
        ];
    }

    #[DataProvider('locales')]
    public function test_interface_automatically_uses_the_application_locale(string $locale, string $label): void
    {
        $this->app->setLocale($locale);
        $this->actingAs($this->staff(['player-notes.view']));
        $this->get(route('player-notes.admin.index'))->assertOk()->assertSee($label);
    }

    #[DataProvider('locales')]
    public function test_translations_are_complete_and_preserve_all_placeholders(string $locale, string $label): void
    {
        $directory = dirname(__DIR__, 2).'/resources/lang/';
        $source = Arr::dot(require $directory.'en/admin.php');
        $translated = Arr::dot(require $directory.$locale.'/admin.php');

        $this->assertSame(array_keys($source), array_keys($translated));
        $this->assertSame($label, $translated['all_notes']);

        foreach ($source as $key => $text) {
            $this->assertIsString($translated[$key]);
            $this->assertNotSame('', trim($translated[$key]));
            $this->assertTrue(mb_check_encoding($translated[$key], 'UTF-8'));
            preg_match_all('/:[a-z_]+/i', $text, $expected);
            preg_match_all('/:[a-z_]+/i', $translated[$key], $actual);
            sort($expected[0]);
            sort($actual[0]);
            $this->assertSame($expected[0], $actual[0], $locale.': '.$key);
        }
    }

    public function test_locale_changes_are_applied_without_rebooting_the_provider(): void
    {
        $routes = plugins()->getRouteDescriptions();
        $this->assertTrue($routes->has('player-notes.admin.index'));
        $description = $routes->get('player-notes.admin.index');

        foreach (['de' => 'Spielernotizen', 'es_ES' => 'Notas de jugadores', 'fr' => 'Player Notes'] as $locale => $title) {
            $this->app->setLocale($locale);
            $this->assertSame($title, trans($description));
        }
    }

    public function test_navbar_plugin_selector_contains_the_protected_player_notes_route(): void
    {
        $this->actingAs($this->staff([], true));
        $this->get(route('admin.navbar-elements.create'))->assertOk()
            ->assertSee('<option value="player-notes.admin.index"', false);

        $element = new NavbarElement(['type' => 'plugin', 'value' => 'player-notes.admin.index']);
        $this->assertSame(route('player-notes.admin.index'), $element->getLink());
        $this->get($element->getLink())->assertOk();

        $this->app['auth']->forgetGuards();
        $this->get($element->getLink())->assertRedirect(route('login'));
    }

    public function test_untranslated_locale_uses_the_configured_fallback(): void
    {
        $this->app->setLocale('it');
        $this->assertSame('All notes', trans('player-notes::admin.all_notes'));
    }
}
