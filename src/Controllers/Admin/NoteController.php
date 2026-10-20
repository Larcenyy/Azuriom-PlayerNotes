<?php

namespace Azuriom\Plugin\UserNotes\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\User;
use Azuriom\Plugin\UserNotes\Models\Note;
use Azuriom\Plugin\UserNotes\Models\Tag;
use Azuriom\Plugin\UserNotes\Support\NoteFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $notes = NoteFilters::apply(Note::query(), $request)
            ->with(['user', 'author', 'editor', 'tags'])->paginate(15)->withQueryString();

        return view('user-notes::admin.index', [
            'notes' => $notes,
            'tags' => Tag::orderBy('name')->get(),
            'authors' => User::whereIn('id', Note::select('author_id'))->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, User $user)
    {
        abort_if($user->isDeleted(), 404);
        $data = $this->validateNote($request);

        DB::transaction(function () use ($request, $user, $data) {
            $note = new Note(Arr::only($data, ['title', 'content']));
            $note->user()->associate($user);
            $note->author()->associate($request->user());
            $note->editor()->associate($request->user());
            $note->save();
            $note->tags()->sync($data['tags'] ?? []);
            $this->recordRevision($note, 'created');
        });

        return to_route('user-notes.admin.users.show', $user)
            ->with('success', trans('user-notes::admin.saved'));
    }

    public function edit(User $user, Note $note)
    {
        $this->assertUser($user, $note);
        $this->authorize('update', $note);
        $note->load('tags');

        return view('user-notes::admin.edit', [
            'user' => $user,
            'note' => $note,
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user, Note $note)
    {
        $this->assertUser($user, $note);
        $this->authorize('update', $note);
        $data = $this->validateNote($request, true);

        DB::transaction(function () use ($request, $note, $data) {
            $locked = $this->lockNote($request, $note);
            $locked->fill(Arr::only($data, ['title', 'content']));
            $locked->editor()->associate($request->user());
            $locked->version++;
            $locked->save();
            $locked->tags()->sync($data['tags'] ?? []);
            $this->recordRevision($locked, 'updated');
        });

        return to_route('user-notes.admin.users.show', $user)
            ->with('success', trans('user-notes::admin.saved'));
    }

    public function destroy(Request $request, User $user, Note $note)
    {
        $this->assertUser($user, $note);
        $this->authorize('delete', $note);
        $request->validate(['version' => ['required', 'integer', 'min:1']]);

        DB::transaction(fn () => $this->lockNote($request, $note)->delete());

        return to_route('user-notes.admin.users.show', $user)
            ->with('success', trans('user-notes::admin.deleted'));
    }

    public function pin(Request $request, User $user, Note $note)
    {
        $this->assertUser($user, $note);
        $request->validate([
            'version' => ['required', 'integer', 'min:1'],
            'is_pinned' => ['required', 'boolean'],
        ]);

        DB::transaction(function () use ($request, $note) {
            $locked = $this->lockNote($request, $note);

            if ($locked->is_pinned === $request->boolean('is_pinned')) {
                return;
            }

            $locked->is_pinned = $request->boolean('is_pinned');
            $locked->editor()->associate($request->user());
            $locked->version++;
            $locked->save();
            $this->recordRevision($locked, $locked->is_pinned ? 'pinned' : 'unpinned');
        });

        return to_route('user-notes.admin.users.show', $user)
            ->with('success', trans('user-notes::admin.saved'));
    }

    public function history(User $user, Note $note)
    {
        $this->assertUser($user, $note);

        return view('user-notes::admin.history', [
            'user' => $user,
            'note' => $note,
            'revisions' => $note->revisions()->with('editor')->orderByDesc('version')->paginate(15),
        ]);
    }

    private function assertUser(User $user, Note $note): void
    {
        abort_unless($note->user_id === $user->id, 404);
    }

    private function validateNote(Request $request, bool $updating = false): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150', 'regex:/\S/u'],
            'content' => ['required', 'string', 'max:10000', 'regex:/\S/u'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['required', 'integer', 'distinct', 'exists:user_notes_tags,id'],
            'version' => [$updating ? 'required' : 'prohibited', 'integer', 'min:1'],
        ]);
    }

    private function lockNote(Request $request, Note $note): Note
    {
        $locked = Note::lockForUpdate()->findOrFail($note->id);

        if ($locked->version !== $request->integer('version')) {
            throw ValidationException::withMessages(['version' => trans('user-notes::admin.conflict')]);
        }

        return $locked;
    }

    private function recordRevision(Note $note, string $action): void
    {
        $note->revisions()->create([
            'editor_id' => $note->editor_id,
            'version' => $note->version,
            'action' => $action,
            'title' => $note->title,
            'content' => $note->content,
            'is_pinned' => $note->is_pinned ?? false,
            'tags' => $note->tags()->orderBy('name')->pluck('name')->all(),
        ]);
    }
}
