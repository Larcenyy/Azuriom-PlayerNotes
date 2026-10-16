# Player Notes

Private player notes for Azuriom staff, with tags, pinned notes, search and revision history. Uses the existing Bootstrap 5 administration layout without custom CSS or JavaScript.

## Installation

1. Extract the plugin into `plugins/player-notes/`.
2. Enable **Player Notes** from the administration plugin page.
3. Configure staff permissions under **Roles**.
4. Open **Player Notes → Players** and select a player to add notes.

Requires Azuriom 1.2.x and PHP 8.2 or newer. No additional dependencies or asset compilation are required.

## Permissions

Staff require both `admin.access` and `player-notes.view` to access the plugin.

| Permission | Access |
| --- | --- |
| `player-notes.view` | Read notes and revision history. |
| `player-notes.create` | Create notes. |
| `player-notes.edit` | Edit own notes. |
| `player-notes.delete` | Delete own notes and their history permanently. |
| `player-notes.moderate` | Extend granted edit/delete permissions to other authors' notes. |
| `player-notes.pin` | Pin and unpin notes. |
| `player-notes.tags` | Manage shared tags. |

Administrators have full access. Authorized readers can see all player dossiers. Disabling the plugin preserves its data.

## Languages and navigation

The interface automatically follows the site's language: English (`en`), French (`fr`), German (`de`), Spanish (`es_ES`), Brazilian Portuguese (`pt_BR`), Dutch (`nl`), Polish (`pl`) and Russian (`ru`). Note contents and custom tag names remain as entered by staff.

To add a website navigation link, select **Plugin → Player Notes** when creating a navbar element. The link opens the protected administration page. Use the navbar role settings to display it only to staff.

## License

MIT. See [LICENSE](LICENSE).
