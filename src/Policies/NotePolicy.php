<?php

namespace Azuriom\Plugin\UserNotes\Policies;

use Azuriom\Models\User;
use Azuriom\Plugin\UserNotes\Models\Note;

class NotePolicy
{
    public function update(User $user, Note $note): bool
    {
        return $user->can('user-notes.view') && $user->can('user-notes.edit')
            && ($note->author_id === $user->id || $user->can('user-notes.moderate'));
    }

    public function delete(User $user, Note $note): bool
    {
        return $user->can('user-notes.view') && $user->can('user-notes.delete')
            && ($note->author_id === $user->id || $user->can('user-notes.moderate'));
    }
}
