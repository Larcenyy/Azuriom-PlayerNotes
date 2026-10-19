# User Notes

Private user notes for Azuriom staff, with tags, pinned notes, search and revision history.

## Installation

1. Extract the plugin into `plugins/player-notes/`.
2. Enable **User Notes** from the administration plugin page.
3. Configure staff permissions under **Roles**.
4. Open **User Notes → Users** and select a user to add notes.

Requires Azuriom 1.2.x and PHP 8.2 or newer.

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

Administrators have full access. Authorized readers can see all user dossiers. Disabling the plugin preserves its data.

## Languages and navigation

Available languages: English (`en`), French (`fr`), German (`de`), Spanish (`es_ES`), Brazilian Portuguese (`pt_BR`), Dutch (`nl`), Polish (`pl`) and Russian (`ru`).

To add a website navigation link, select **Plugin → User Notes** when creating a navbar element. Use the navbar role settings to display it only to staff.

The existing `player-notes` folder, permission names and database tables are retained for compatibility with installed versions.

## License

MIT. See [LICENSE](LICENSE).
