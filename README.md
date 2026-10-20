# User Notes

Private user notes for Azuriom staff, with tags, pinned notes, search and revision history.

## Installation

1. Extract the plugin into `plugins/user-notes/`.
2. Enable **User Notes** from the administration plugin page.
3. Configure staff permissions under **Roles**.
4. Open **User Notes → Users** and select a user to add notes.

Requires Azuriom 1.2.x and PHP 8.2 or newer.

## Permissions

Staff require both `admin.access` and `user-notes.view` to access the plugin.

| Permission | Access |
| --- | --- |
| `user-notes.view` | Read notes and revision history. |
| `user-notes.create` | Create notes. |
| `user-notes.edit` | Edit own notes. |
| `user-notes.delete` | Delete own notes and their history permanently. |
| `user-notes.moderate` | Extend granted edit/delete permissions to other authors' notes. |
| `user-notes.pin` | Pin and unpin notes. |
| `user-notes.tags` | Manage shared tags. |

Administrators have full access. Authorized readers can see all user dossiers. Disabling the plugin preserves its data.

## Languages and navigation

Available languages: English (`en`), French (`fr`), German (`de`), Spanish (`es_ES`), Brazilian Portuguese (`pt_BR`), Dutch (`nl`), Polish (`pl`) and Russian (`ru`).

To add a website navigation link, select **Plugin → User Notes** when creating a navbar element. Use the navbar role settings to display it only to staff.

## License

MIT. See [LICENSE](LICENSE).

Source: https://github.com/Larcenyy/Azuriom-PlayerNotes
