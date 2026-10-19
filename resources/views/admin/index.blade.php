@extends('admin.layouts.admin')

@section('title', trans('user-notes::admin.title'))

@section('content')
    @include('user-notes::admin._navigation')
    <div class="d-flex justify-content-end mb-3">
        <a class="btn btn-primary" href="{{ route('user-notes.admin.users.index') }}"><i class="bi bi-person-search" aria-hidden="true"></i> {{ trans('user-notes::admin.open_dossier') }}</a>
    </div>
    @include('user-notes::admin._filters', ['filterAction' => route('user-notes.admin.index')])
    @include('user-notes::admin._notes')
@endsection
