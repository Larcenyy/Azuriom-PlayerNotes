@extends('admin.layouts.admin')

@section('title', trans('user-notes::admin.dossier', ['user' => $user->name]))

@section('content')
    @include('user-notes::admin._navigation')
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="h5 text-break">{{ $user->name }}</h2>
                <p class="text-muted mb-0">{{ trans('user-notes::admin.account_id') }}: {{ $user->id }}</p>
                @if($user->game_id)
                    <p class="text-muted mb-0 text-break">{{ trans('user-notes::admin.game_id') }}: {{ $user->game_id }}</p>
                @endif
            </div>
            @can('admin.users')
                <a class="btn btn-outline-secondary" href="{{ route('admin.users.edit', $user) }}">{{ trans('user-notes::admin.profile') }}</a>
            @endcan
        </div>
    </div>
    @if($user->isDeleted())
        <div class="alert alert-warning" role="alert">{{ trans('user-notes::admin.deleted_player') }}</div>
    @else
        @can('user-notes.create')
            <div class="card shadow-sm mb-4" id="addNote">
                <div class="card-header"><h2 class="h5 mb-0">{{ trans('user-notes::admin.add_note') }}</h2></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user-notes.admin.notes.store', $user) }}">
                        @csrf
                        @include('user-notes::admin._note-form')
                    </form>
                </div>
            </div>
        @endcan
    @endif
    @include('user-notes::admin._filters', ['filterAction' => route('user-notes.admin.users.show', $user)])
    @include('user-notes::admin._notes', ['emptyMessage' => 'user-notes::admin.no_player_notes'])
@endsection
