@forelse($notes as $entry)
    <article class="card shadow-sm mb-3 {{ $entry->is_pinned ? 'border-warning' : '' }}" aria-labelledby="noteHeading{{ $entry->id }}">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 id="noteHeading{{ $entry->id }}" class="h5 mb-0 text-break">
                @if($entry->is_pinned)
                    <span class="badge bg-warning text-dark"><i class="bi bi-pin-angle-fill" aria-hidden="true"></i> {{ trans('user-notes::admin.pinned') }}</span>
                @endif
                {{ $entry->title }}
            </h2>
            <a href="{{ route('user-notes.admin.users.show', $entry->player_id) }}" class="text-break">{{ $entry->user->name }}</a>
        </div>
        <div class="card-body">
            <div class="text-break mb-3">{!! nl2br(e($entry->content)) !!}</div>
            @if($entry->tags->isNotEmpty())
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach($entry->tags as $tag)
                        @include('user-notes::admin._tag')
                    @endforeach
                </div>
            @endif
            <p class="small text-muted mb-1">
                {{ trans('user-notes::admin.author') }}: {{ $entry->author?->name ?? trans('user-notes::admin.deleted_user') }}
                · {{ trans('user-notes::admin.created_at', ['date' => format_date_compact($entry->created_at)]) }}
            </p>
            @if($entry->version > 1)
                <p class="small text-muted mb-1">{{ trans('user-notes::admin.edited_by', ['author' => $entry->editor?->name ?? trans('user-notes::admin.deleted_user'), 'date' => format_date_compact($entry->updated_at)]) }}</p>
            @endif
        </div>
        <div class="card-footer d-flex flex-wrap gap-2">
            @can('update', $entry)
                <a class="btn btn-outline-primary btn-sm" href="{{ route('user-notes.admin.notes.edit', [$entry->player_id, $entry]) }}">{{ trans('user-notes::admin.edit') }}</a>
            @endcan
            @can('user-notes.pin')
                <form action="{{ route('user-notes.admin.notes.pin', [$entry->player_id, $entry]) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="version" value="{{ $entry->version }}">
                    <input type="hidden" name="is_pinned" value="{{ $entry->is_pinned ? '0' : '1' }}">
                    <button type="submit" class="btn btn-outline-secondary btn-sm">{{ trans($entry->is_pinned ? 'user-notes::admin.unpin' : 'user-notes::admin.pin') }}</button>
                </form>
            @endcan
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('user-notes.admin.notes.history', [$entry->player_id, $entry]) }}">{{ trans('user-notes::admin.history') }}</a>
            @can('delete', $entry)
                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteNote{{ $entry->id }}">{{ trans('user-notes::admin.delete') }}</button>
            @endcan
        </div>
    </article>
    @can('delete', $entry)
        <div class="modal fade" id="deleteNote{{ $entry->id }}" tabindex="-1" aria-labelledby="deleteNoteTitle{{ $entry->id }}" aria-hidden="true">
            <div class="modal-dialog"><div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="deleteNoteTitle{{ $entry->id }}">{{ trans('user-notes::admin.delete_note_title') }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('user-notes::admin.close') }}"></button>
                </div>
                <div class="modal-body text-break">{{ trans('user-notes::admin.delete_note_body', ['title' => $entry->title]) }}</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('user-notes::admin.cancel') }}</button>
                    <form method="POST" action="{{ route('user-notes.admin.notes.destroy', [$entry->player_id, $entry]) }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="version" value="{{ $entry->version }}">
                        <button type="submit" class="btn btn-danger">{{ trans('user-notes::admin.delete') }}</button>
                    </form>
                </div>
            </div></div>
        </div>
    @endcan
@empty
    <div class="card shadow-sm mb-4"><div class="card-body text-center py-5 text-muted">{{ trans($emptyMessage ?? 'user-notes::admin.no_notes') }}</div></div>
@endforelse
{{ $notes->links() }}
