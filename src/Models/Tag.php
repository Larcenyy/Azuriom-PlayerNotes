<?php

namespace Azuriom\Plugin\UserNotes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    public const COLORS = ['secondary', 'primary', 'success', 'danger', 'warning', 'info', 'dark'];

    protected $table = 'user_notes_tags';

    protected $fillable = ['name', 'color'];

    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(Note::class, 'user_notes_note_tag', 'tag_id', 'note_id');
    }
}
