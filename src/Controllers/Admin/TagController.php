<?php

namespace Azuriom\Plugin\UserNotes\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\UserNotes\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index()
    {
        return view('user-notes::admin.tags.index', [
            'tags' => Tag::withCount('notes')->orderBy('name')->paginate(25),
            'tag' => new Tag(['color' => 'secondary']),
        ]);
    }

    public function store(Request $request)
    {
        Tag::create($this->validateTag($request));

        return to_route('user-notes.admin.tags.index')->with('success', trans('user-notes::admin.saved'));
    }

    public function edit(Tag $tag)
    {
        return view('user-notes::admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $tag->update($this->validateTag($request, $tag));

        return to_route('user-notes.admin.tags.index')->with('success', trans('user-notes::admin.saved'));
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return to_route('user-notes.admin.tags.index')->with('success', trans('user-notes::admin.deleted'));
    }

    private function validateTag(Request $request, ?Tag $tag = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:50', 'regex:/\S/u', Rule::unique('player_notes_tags')->ignore($tag)],
            'color' => ['required', Rule::in(Tag::COLORS)],
        ]);
    }
}
