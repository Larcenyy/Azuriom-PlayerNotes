<?php

namespace Azuriom\Plugin\PlayerNotes\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\User;
use Azuriom\Plugin\PlayerNotes\Models\Note;
use Azuriom\Plugin\PlayerNotes\Models\Tag;
use Azuriom\Plugin\PlayerNotes\Support\NoteFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['search' => ['nullable', 'string', 'max:150']]);
        $search = $data['search'] ?? '';

        $players = User::query()->whereNull('deleted_at')
            ->when($search !== '', function (Builder $query) use ($search) {
                $pattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search).'%';

                $query->where(function (Builder $query) use ($pattern, $search) {
                    $query->whereRaw("name LIKE ? ESCAPE '!'", [$pattern])->orWhereRaw("game_id LIKE ? ESCAPE '!'", [$pattern]);

                    if (ctype_digit($search)) {
                        $query->orWhere('id', $search);
                    }
                });
            })
            ->addSelect(['notes_count' => Note::selectRaw('count(*)')->whereColumn('player_id', 'users.id')])
            ->orderBy('name')->orderBy('id')->paginate(20)->withQueryString();

        return view('player-notes::admin.players', compact('players', 'search'));
    }

    public function show(Request $request, User $player)
    {
        $notes = NoteFilters::apply(Note::where('player_id', $player->id), $request)
            ->with(['player', 'author', 'editor', 'tags'])->paginate(15)->withQueryString();

        return view('player-notes::admin.dossier', [
            'player' => $player,
            'notes' => $notes,
            'tags' => Tag::orderBy('name')->get(),
            'authors' => User::whereIn('id', Note::select('author_id')->where('player_id', $player->id))
                ->orderBy('name')->get(),
            'note' => new Note(),
        ]);
    }
}
