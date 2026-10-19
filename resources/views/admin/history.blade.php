@extends('admin.layouts.admin')

@section('title', trans('user-notes::admin.history_title'))

@section('content')
    @include('user-notes::admin._navigation')
    <a class="btn btn-outline-secondary mb-3" href="{{ route('user-notes.admin.users.show', $user) }}">{{ trans('user-notes::admin.back') }}</a>
    <h2 class="h5 text-break mb-3">{{ $user->name }} · {{ $note->title }}</h2>
    @foreach($revisions as $revision)
        <details class="card shadow-sm mb-3" @if($loop->first) open @endif>
            <summary class="card-header">
                {{ trans('user-notes::admin.revision', ['version' => $revision->version]) }}
                · {{ trans('user-notes::admin.events.'.$revision->action) }}
                · {{ $revision->editor?->name ?? trans('user-notes::admin.deleted_user') }}
                · {{ format_date_compact($revision->created_at) }}
            </summary>
            <div class="card-body">
                <h3 class="h5 text-break">{{ $revision->title }}</h3>
                <div class="text-break mb-3">{!! nl2br(e($revision->content)) !!}</div>
                <div class="d-flex flex-wrap gap-2">
                    @if($revision->is_pinned)<span class="badge bg-warning text-dark">{{ trans('user-notes::admin.pinned') }}</span>@endif
                    @foreach($revision->tags as $tagName)<span class="badge bg-secondary text-white text-wrap text-break">{{ $tagName }}</span>@endforeach
                </div>
            </div>
        </details>
    @endforeach
    {{ $revisions->links() }}
@endsection
