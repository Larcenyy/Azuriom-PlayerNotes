<div class="alert alert-info" role="note">
    <i class="bi bi-shield-lock" aria-hidden="true"></i> {{ trans('user-notes::admin.private') }}
</div>

<nav class="nav nav-pills flex-wrap gap-2 mb-4" aria-label="{{ trans('user-notes::admin.title') }}">
    <a class="nav-link {{ Route::is('user-notes.admin.index') ? 'active' : '' }}" href="{{ route('user-notes.admin.index') }}" @if(Route::is('user-notes.admin.index')) aria-current="page" @endif>
        {{ trans('user-notes::admin.all_notes') }}
    </a>
    <a class="nav-link {{ Route::is('user-notes.admin.users.*', 'user-notes.admin.notes.*') ? 'active' : '' }}" href="{{ route('user-notes.admin.users.index') }}" @if(Route::is('user-notes.admin.users.*', 'user-notes.admin.notes.*')) aria-current="page" @endif>
        {{ trans('user-notes::admin.users') }}
    </a>
    @can('user-notes.tags')
        <a class="nav-link {{ Route::is('user-notes.admin.tags.*') ? 'active' : '' }}" href="{{ route('user-notes.admin.tags.index') }}" @if(Route::is('user-notes.admin.tags.*')) aria-current="page" @endif>
            {{ trans('user-notes::admin.tags') }}
        </a>
    @endcan
</nav>
