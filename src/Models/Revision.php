<?php

namespace Azuriom\Plugin\PlayerNotes\Models;

use Azuriom\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revision extends Model
{
    protected $table = 'player_notes_revisions';

    protected $fillable = ['editor_id', 'version', 'action', 'title', 'content', 'is_pinned', 'tags'];

    protected $casts = ['tags' => 'array', 'is_pinned' => 'boolean', 'version' => 'integer'];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }
}
