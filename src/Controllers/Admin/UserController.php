<?php

namespace Azuriom\Plugin\UserNotes\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\User;
use Azuriom\Plugin\UserNotes\Models\Note;
use Azuriom\Plugin\UserNotes\Models\Tag;
use Azuriom\Plugin\UserNotes\Support\NoteFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['search' => ['nullable', 'string', 'max:150']]);
        $search = $data['search'] ?? '';

        $users = User::query()->whereNull('deleted_at')
            ->when($search !== '', function (Builder $query) use ($search) {
                $pattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search).'%';

                $query->where(function (Builder $query) use ($pattern, $search) {
                    $query->whereRaw("name LIKE ? ESCAPE '!'", [$pattern])->orWhereRaw("game_id LIKE ? ESCAPE '!'", [$pattern]);

                    if (ctype_digit($search)) {
                        $query->orWhere('id', $search);
                    }
                });
            })
            ->addSelect(['notes_count' => Note::selectRaw('count(*)')->whereColumn('user_id', 'users.id')])
            ->orderBy('name')->orderBy('id')->paginate(20)->withQueryString();

        return view('user-notes::admin.users', compact('users', 'search'));
    }

    public function show(Request $request, User $user)
    {
        $notes = NoteFilters::apply(Note::where('user_id', $user->id), $request)
            ->with(['user', 'author', 'editor', 'tags'])->paginate(15)->withQueryString();

        return view('user-notes::admin.dossier', [
            'user' => $user,
            'notes' => $notes,
            'tags' => Tag::orderBy('name')->get(),
            'authors' => User::whereIn('id', Note::select('author_id')->where('user_id', $user->id))
                ->orderBy('name')->get(),
            'note' => new Note(),
        ]);
    }
}
