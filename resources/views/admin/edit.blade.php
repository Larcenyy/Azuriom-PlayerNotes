@extends('admin.layouts.admin')

@section('title', trans('player-notes::admin.edit_note'))

@section('content')
    @include('player-notes::admin._navigation')
    <a class="btn btn-outline-secondary mb-3" href="{{ route('player-notes.admin.players.show', $player) }}">{{ trans('player-notes::admin.back') }}</a>
    <div class="card shadow-sm mb-4"><div class="card-body">
        <h2 class="h5 mb-3">{{ trans('player-notes::admin.dossier', ['player' => $player->name]) }}</h2>
        <form method="POST" action="{{ route('player-notes.admin.notes.update', [$player, $note]) }}">
            @csrf
            @method('PUT')
            @include('player-notes::admin._note-form')
        </form>
    </div></div>
@endsection
