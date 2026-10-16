@can('player-notes.view')
    <p>{{ trans('player-notes::admin.card_description', ['count' => $playerNotesCount]) }}</p>
    <a class="btn btn-primary" href="{{ route('player-notes.admin.players.show', $user) }}">{{ trans('player-notes::admin.open_dossier') }}</a>
@endcan
