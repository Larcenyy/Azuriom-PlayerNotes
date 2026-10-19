@extends('admin.layouts.admin')

@section('title', trans('user-notes::admin.edit_tag'))

@section('content')
    @include('user-notes::admin._navigation')
    <a class="btn btn-outline-secondary mb-3" href="{{ route('user-notes.admin.tags.index') }}">{{ trans('user-notes::admin.cancel') }}</a>
    <div class="card shadow-sm"><div class="card-body">
        <form method="POST" action="{{ route('user-notes.admin.tags.update', $tag) }}">
            @csrf
            @method('PUT')
            @include('user-notes::admin.tags._form')
        </form>
    </div></div>
@endsection
