<?php

namespace Azuriom\Plugin\PlayerNotes\Policies;

use Azuriom\Models\User;
use Azuriom\Plugin\PlayerNotes\Models\Note;

class NotePolicy
{
    public function update(User $user, Note $note): bool
    {
        return $user->can('player-notes.view') && $user->can('player-notes.edit')
            && ($note->author_id === $user->id || $user->can('player-notes.moderate'));
    }

    public function delete(User $user, Note $note): bool
    {
        return $user->can('player-notes.view') && $user->can('player-notes.delete')
            && ($note->author_id === $user->id || $user->can('player-notes.moderate'));
    }
}
