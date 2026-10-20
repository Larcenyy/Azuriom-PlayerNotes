@extends('admin.layouts.admin')

@section('title', trans('user-notes::admin.users'))

@section('content')
    @include('user-notes::admin._navigation')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('user-notes.admin.users.index') }}" class="row g-3 mb-4">
                <div class="col-12 col-md-9">
                    <label class="form-label" for="userSearch">{{ trans('user-notes::admin.search_users') }}</label>
                    <input class="form-control" type="search" id="userSearch" name="search" maxlength="150" value="{{ $search }}">
                </div>
                <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary" type="submit">{{ trans('user-notes::admin.search') }}</button>
                    <a class="btn btn-outline-secondary" href="{{ route('user-notes.admin.users.index') }}">{{ trans('user-notes::admin.reset') }}</a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr>
                        <th scope="col">{{ trans('user-notes::admin.user') }}</th>
                        <th scope="col">{{ trans('user-notes::admin.account_id') }}</th>
                        <th scope="col">{{ trans('user-notes::admin.game_id') }}</th>
                        <th scope="col">{{ trans('user-notes::admin.notes_count') }}</th>
                        <th scope="col">{{ trans('user-notes::admin.actions') }}</th>
                    </tr></thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <th scope="row" class="text-break">{{ $user->name }}</th>
                            <td>{{ $user->id }}</td>
                            <td class="text-break">{{ $user->game_id ?? '—' }}</td>
                            <td>{{ $user->notes_count }}</td>
                            <td><a class="btn btn-outline-primary btn-sm" href="{{ route('user-notes.admin.users.show', $user) }}">{{ trans('user-notes::admin.open_dossier') }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted text-center py-4">{{ trans('user-notes::admin.no_users') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        </div>
    </div>
@endsection
