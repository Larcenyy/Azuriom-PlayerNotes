@extends('admin.layouts.admin')

@section('title', trans('player-notes::admin.dossier', ['player' => $player->name]))

@section('content')
    @include('player-notes::admin._navigation')
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="h5 text-break">{{ $player->name }}</h2>
                <p class="text-muted mb-0">{{ trans('player-notes::admin.account_id') }}: {{ $player->id }}</p>
                @if($player->game_id)
                    <p class="text-muted mb-0 text-break">{{ trans('player-notes::admin.game_id') }}: {{ $player->game_id }}</p>
                @endif
            </div>
            @can('admin.users')
                <a class="btn btn-outline-secondary" href="{{ route('admin.users.edit', $player) }}">{{ trans('player-notes::admin.profile') }}</a>
            @endcan
        </div>
    </div>
    @if($player->isDeleted())
        <div class="alert alert-warning" role="alert">{{ trans('player-notes::admin.deleted_player') }}</div>
    @else
        @can('player-notes.create')
            <div class="card shadow-sm mb-4" id="addNote">
                <div class="card-header"><h2 class="h5 mb-0">{{ trans('player-notes::admin.add_note') }}</h2></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('player-notes.admin.notes.store', $player) }}">
                        @csrf
                        @include('player-notes::admin._note-form')
                    </form>
                </div>
            </div>
        @endcan
    @endif
    @include('player-notes::admin._filters', ['filterAction' => route('player-notes.admin.players.show', $player)])
    @include('player-notes::admin._notes', ['emptyMessage' => 'player-notes::admin.no_player_notes'])
@endsection
