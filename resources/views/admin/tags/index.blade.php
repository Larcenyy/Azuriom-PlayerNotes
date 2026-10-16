@extends('admin.layouts.admin')

@section('title', trans('player-notes::admin.tags'))

@section('content')
    @include('player-notes::admin._navigation')
    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm"><div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr>
                            <th scope="col">{{ trans('player-notes::admin.tag_name') }}</th>
                            <th scope="col">{{ trans('player-notes::admin.notes_count') }}</th>
                            <th scope="col">{{ trans('player-notes::admin.actions') }}</th>
                        </tr></thead>
                        <tbody>
                        @forelse($tags as $entry)
                            <tr>
                                <th scope="row">@include('player-notes::admin._tag', ['tag' => $entry])</th>
                                <td>{{ $entry->notes_count }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a class="btn btn-outline-primary btn-sm" href="{{ route('player-notes.admin.tags.edit', $entry) }}">{{ trans('player-notes::admin.edit') }}</a>
                                        <button class="btn btn-outline-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#deleteTag{{ $entry->id }}">{{ trans('player-notes::admin.delete') }}</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted text-center py-4">{{ trans('player-notes::admin.no_tags') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $tags->links() }}
            </div></div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header"><h2 class="h5 mb-0">{{ trans('player-notes::admin.add_tag') }}</h2></div>
                <div class="card-body"><form method="POST" action="{{ route('player-notes.admin.tags.store') }}">
                    @csrf
                    @include('player-notes::admin.tags._form')
                </form></div>
            </div>
        </div>
    </div>
    @foreach($tags as $entry)
        <div class="modal fade" id="deleteTag{{ $entry->id }}" tabindex="-1" aria-labelledby="deleteTagTitle{{ $entry->id }}" aria-hidden="true">
            <div class="modal-dialog"><div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="deleteTagTitle{{ $entry->id }}">{{ trans('player-notes::admin.delete_tag_title') }}</h2>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="{{ trans('player-notes::admin.close') }}"></button>
                </div>
                <div class="modal-body text-break">{{ trans('player-notes::admin.delete_tag_body', ['name' => $entry->name]) }}</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{ trans('player-notes::admin.cancel') }}</button>
                    <form method="POST" action="{{ route('player-notes.admin.tags.destroy', $entry) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" type="submit">{{ trans('player-notes::admin.delete') }}</button>
                    </form>
                </div>
            </div></div>
        </div>
    @endforeach
@endsection
