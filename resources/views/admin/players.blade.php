@extends('admin.layouts.admin')

@section('title', trans('player-notes::admin.players'))

@section('content')
    @include('player-notes::admin._navigation')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('player-notes.admin.players.index') }}" class="row g-3 mb-4">
                <div class="col-12 col-md-9">
                    <label class="form-label" for="playerSearch">{{ trans('player-notes::admin.search_players') }}</label>
                    <input class="form-control" type="search" id="playerSearch" name="search" maxlength="150" value="{{ $search }}">
                </div>
                <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary" type="submit">{{ trans('player-notes::admin.search') }}</button>
                    <a class="btn btn-outline-secondary" href="{{ route('player-notes.admin.players.index') }}">{{ trans('player-notes::admin.reset') }}</a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr>
                        <th scope="col">{{ trans('player-notes::admin.player') }}</th>
                        <th scope="col">{{ trans('player-notes::admin.account_id') }}</th>
                        <th scope="col">{{ trans('player-notes::admin.game_id') }}</th>
                        <th scope="col">{{ trans('player-notes::admin.notes_count') }}</th>
                        <th scope="col">{{ trans('player-notes::admin.actions') }}</th>
                    </tr></thead>
                    <tbody>
                    @forelse($players as $player)
                        <tr>
                            <th scope="row" class="text-break">{{ $player->name }}</th>
                            <td>{{ $player->id }}</td>
                            <td class="text-break">{{ $player->game_id ?? '—' }}</td>
                            <td>{{ $player->notes_count }}</td>
                            <td><a class="btn btn-outline-primary btn-sm" href="{{ route('player-notes.admin.players.show', $player) }}">{{ trans('player-notes::admin.open_dossier') }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted text-center py-4">{{ trans('player-notes::admin.no_players') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $players->links() }}
        </div>
    </div>
@endsection
