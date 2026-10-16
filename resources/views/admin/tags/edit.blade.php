@extends('admin.layouts.admin')

@section('title', trans('player-notes::admin.edit_tag'))

@section('content')
    @include('player-notes::admin._navigation')
    <a class="btn btn-outline-secondary mb-3" href="{{ route('player-notes.admin.tags.index') }}">{{ trans('player-notes::admin.cancel') }}</a>
    <div class="card shadow-sm"><div class="card-body">
        <form method="POST" action="{{ route('player-notes.admin.tags.update', $tag) }}">
            @csrf
            @method('PUT')
            @include('player-notes::admin.tags._form')
        </form>
    </div></div>
@endsection
