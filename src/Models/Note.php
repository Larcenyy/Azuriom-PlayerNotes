<?php

namespace Azuriom\Plugin\UserNotes\Models;

use Azuriom\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Note extends Model
{
    protected $table = 'user_notes';

    protected $fillable = ['title', 'content'];

    protected $attributes = ['version' => 1, 'is_pinned' => false];

    protected $casts = ['is_pinned' => 'boolean', 'version' => 'integer'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'user_notes_note_tag', 'note_id', 'tag_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class);
    }
}
