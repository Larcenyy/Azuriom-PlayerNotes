@can('user-notes.view')
    <p>{{ trans('user-notes::admin.card_description', ['count' => $userNotesCount]) }}</p>
    <a class="btn btn-primary" href="{{ route('user-notes.admin.users.show', $user) }}">{{ trans('user-notes::admin.open_dossier') }}</a>
@endcan
