<div class="alert alert-info" role="note">
    <i class="bi bi-shield-lock" aria-hidden="true"></i> {{ trans('player-notes::admin.private') }}
</div>

<nav class="nav nav-pills flex-wrap gap-2 mb-4" aria-label="{{ trans('player-notes::admin.title') }}">
    <a class="nav-link {{ Route::is('player-notes.admin.index') ? 'active' : '' }}" href="{{ route('player-notes.admin.index') }}" @if(Route::is('player-notes.admin.index')) aria-current="page" @endif>
        {{ trans('player-notes::admin.all_notes') }}
    </a>
    <a class="nav-link {{ Route::is('player-notes.admin.players.*', 'player-notes.admin.notes.*') ? 'active' : '' }}" href="{{ route('player-notes.admin.players.index') }}" @if(Route::is('player-notes.admin.players.*', 'player-notes.admin.notes.*')) aria-current="page" @endif>
        {{ trans('player-notes::admin.players') }}
    </a>
    @can('player-notes.tags')
        <a class="nav-link {{ Route::is('player-notes.admin.tags.*') ? 'active' : '' }}" href="{{ route('player-notes.admin.tags.index') }}" @if(Route::is('player-notes.admin.tags.*')) aria-current="page" @endif>
            {{ trans('player-notes::admin.tags') }}
        </a>
    @endcan
</nav>
