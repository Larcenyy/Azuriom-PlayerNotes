# Player Notes

Private player dossiers for Azuriom staff. Keep moderation context, support follow-ups and refund references in one place.

## Features

- Private notes with a title, plain-text content, author and timestamps.
- Search notes by title, content, player name or game ID.
- Find players by name, account ID or game ID, including players without notes.
- Filter by tag, author and pinned status. Pinned notes appear first.
- Assign up to 10 customizable tags per note using Bootstrap's standard colors.
- Separate permissions for reading, creating, editing, deleting, moderating, pinning and managing tags.
- Version history records the content, editor, pinned state and tag names at every change.
- Concurrent changes are rejected instead of silently overwriting another staff member's work.
- A link to the private dossier appears on the existing administration user edit page.
- Automatic interface language selection with English, French, German, Spanish, Brazilian Portuguese, Dutch, Polish and Russian translations.
- Responsive Bootstrap 5 components and accessible form labels.
- No custom CSS, JavaScript, public routes, API controller, external service or additional runtime dependency.

## Requirements

- Azuriom 1.2.x with PHP 8.2 or newer.
- The Bootstrap 5 administration layout and `AdminUserEditComposer` extension point.
- A database supported by Azuriom, with foreign key support enabled.

The plugin uses Azuriom's existing user accounts and works independently of the selected game.

## Installation

1. Place the plugin files in `plugins/player-notes/`. Both `plugin.json` and `composer.json` must be directly inside that directory.
2. Enable **Player Notes** from Azuriom's administration plugin page. Azuriom runs the plugin migrations automatically. Alternatively, run `php artisan plugin:enable player-notes` from the Azuriom root.
3. Configure role permissions under **Roles**. Staff need both `admin.access` and `player-notes.view` to enter the plugin.
4. Open **Player Notes** in the administration sidebar, choose **Players**, then open a player's dossier.
5. Grant `player-notes.tags` to the staff who should create labels such as Moderation, Support or Billing.

No asset compilation or `composer install` inside the plugin is required for normal use.

## Languages

Like the Contact plugin, Player Notes uses Azuriom's native translation files in `resources/lang`. The interface automatically follows the site's configured locale, with Azuriom's configured fallback for languages without a translation. No translation service or API key is required.

| Language | Azuriom locale |
| --- | --- |
| English | `en` |
| French | `fr` |
| German | `de` |
| Spanish | `es_ES` |
| Brazilian Portuguese | `pt_BR` |
| Dutch | `nl` |
| Polish | `pl` |
| Russian | `ru` |

Player names, note contents and custom tag names are entered by staff and remain as written.

## Website navigation

Under **Navigation → Create navbar element**, select **Plugin**, then **Player Notes** (or its translated name) in the link list. The provider registers this entry using the same route-description mechanism as Contact.

This link opens the existing protected administration page. Visitors must sign in and have both `admin.access` and `player-notes.view`. To show the link only to staff, restrict the navbar element to the appropriate roles using Azuriom's standard visibility settings.

## Permissions

| Permission | Access |
| --- | --- |
| `player-notes.view` | Read notes and their history; search players and notes. Required for all plugin operations. |
| `player-notes.create` | Add a note to an active player's dossier. |
| `player-notes.edit` | Edit notes written by the current staff member. |
| `player-notes.delete` | Permanently delete notes written by the current staff member. |
| `player-notes.moderate` | Extend the granted edit/delete permissions to other authors' notes. This permission alone grants neither operation. |
| `player-notes.pin` | Pin or unpin any note, without requiring edit permission. |
| `player-notes.tags` | Create, rename, recolor and delete shared tags. |

Azuriom administrator roles retain their standard full access. All authorized readers can see all player dossiers; there are no per-note or per-role visibility lists.

## History and deletion

Every successful creation, edit, pin or unpin saves a snapshot in a database transaction. Historical tag names are preserved even when a tag is renamed or deleted. History is available only inside the private plugin administration pages; note contents are not copied into Azuriom's general action logs.

Deleting a note permanently removes its revision history and tag associations. Deleting a tag preserves the notes and historical snapshots. Account deletion through Azuriom anonymizes the account; existing notes remain readable and no new note may be added to that deleted account. A physical database deletion of the subject user cascades to their notes. A physical deletion of an author/editor preserves the note and sets that reference to null.

Disabling the plugin preserves its tables. Back up the database before removing data. The migration's `down()` method removes all four plugin tables.

## Development and tests

The test suite runs inside a host Azuriom installation using its Laravel framework and migrations. It forces an isolated in-memory SQLite database and never intentionally migrates the application's configured database. Enable PHP's `pdo_sqlite` extension and install the host's development dependencies first.

From the Azuriom root:

```sh
php vendor/bin/phpunit -c plugins/player-notes/phpunit.xml
```

The tests cover authorization, ownership, moderation, validation, escaped HTML, revisions, concurrency conflicts, pinning, tags, filtering, pagination, language selection, translation keys and placeholders, navbar registration, profile integration and migration rollback.

The plugin exposes only authenticated administration routes under `/admin/player-notes`. All writes use CSRF-protected forms and explicit HTTP verbs. Private pages send `Cache-Control: private, no-store` and `X-Robots-Tag: noindex, nofollow`.

## License

MIT. See [LICENSE](LICENSE).
