<?php

namespace Azuriom\Plugin\UserNotes\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class NoteFilters
{
    public static function apply(Builder $query, Request $request): Builder
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'tag' => ['nullable', 'integer', 'min:1'],
            'author' => ['nullable', 'integer', 'min:1'],
            'pinned' => ['nullable', 'in:1'],
        ]);

        if (isset($filters['search']) && $filters['search'] !== '') {
            $search = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $filters['search']).'%';

            $query->where(function (Builder $query) use ($search) {
                $query->whereRaw("title LIKE ? ESCAPE '!'", [$search])->orWhereRaw("content LIKE ? ESCAPE '!'", [$search])
                    ->orWhereHas('user', function (Builder $query) use ($search) {
                        $query->whereRaw("name LIKE ? ESCAPE '!'", [$search])->orWhereRaw("game_id LIKE ? ESCAPE '!'", [$search]);
                    });
            });
        }

        return $query
            ->when($filters['tag'] ?? null, fn (Builder $q, $tag) => $q->whereHas('tags', fn (Builder $q) => $q->whereKey($tag)))
            ->when($filters['author'] ?? null, fn (Builder $q, $author) => $q->where('author_id', $author))
            ->when($filters['pinned'] ?? null, fn (Builder $q) => $q->where('is_pinned', true))
            ->orderByDesc('is_pinned')->orderByDesc('created_at')->orderByDesc('id');
    }
}
