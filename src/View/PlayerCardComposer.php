<?php

namespace Azuriom\Plugin\PlayerNotes\View;

use Azuriom\Extensions\Plugin\AdminUserEditComposer;
use Azuriom\Models\User;
use Azuriom\Plugin\PlayerNotes\Models\Note;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PlayerCardComposer extends AdminUserEditComposer
{
    public function getCards(User $user, View $view): array
    {
        if (! Gate::allows('player-notes.view')) {
            return [];
        }

        $view->with('playerNotesCount', Note::where('player_id', $user->id)->count());

        return [[
            'name' => trans('player-notes::admin.title'),
            'view' => 'player-notes::admin.player-card',
        ]];
    }
}
