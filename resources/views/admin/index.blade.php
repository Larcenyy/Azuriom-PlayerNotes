@extends('admin.layouts.admin')

@section('title', trans('player-notes::admin.title'))

@section('content')
    @include('player-notes::admin._navigation')
    <div class="d-flex justify-content-end mb-3">
        <a class="btn btn-primary" href="{{ route('player-notes.admin.players.index') }}"><i class="bi bi-person-search" aria-hidden="true"></i> {{ trans('player-notes::admin.open_dossier') }}</a>
    </div>
    @include('player-notes::admin._filters', ['filterAction' => route('player-notes.admin.index')])
    @include('player-notes::admin._notes')
@endsection
