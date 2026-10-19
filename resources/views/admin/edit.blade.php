@extends('admin.layouts.admin')

@section('title', trans('user-notes::admin.edit_note'))

@section('content')
    @include('user-notes::admin._navigation')
    <a class="btn btn-outline-secondary mb-3" href="{{ route('user-notes.admin.users.show', $user) }}">{{ trans('user-notes::admin.back') }}</a>
    <div class="card shadow-sm mb-4"><div class="card-body">
        <h2 class="h5 mb-3">{{ trans('user-notes::admin.dossier', ['user' => $user->name]) }}</h2>
        <form method="POST" action="{{ route('user-notes.admin.notes.update', [$user, $note]) }}">
            @csrf
            @method('PUT')
            @include('user-notes::admin._note-form')
        </form>
    </div></div>
@endsection
