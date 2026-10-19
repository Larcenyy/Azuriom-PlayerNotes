<?php

namespace Azuriom\Plugin\UserNotes\View;

use Azuriom\Extensions\Plugin\AdminUserEditComposer;
use Azuriom\Models\User;
use Azuriom\Plugin\UserNotes\Models\Note;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserCardComposer extends AdminUserEditComposer
{
    public function getCards(User $user, View $view): array
    {
        if (! Gate::allows('user-notes.view')) {
            return [];
        }

        $view->with('userNotesCount', Note::where('player_id', $user->id)->count());

        return [[
            'name' => trans('user-notes::admin.title'),
            'view' => 'user-notes::admin.user-card',
        ]];
    }
}
