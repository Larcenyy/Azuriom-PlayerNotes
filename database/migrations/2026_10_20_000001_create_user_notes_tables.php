<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notes_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('color', 20)->default('secondary');
            $table->timestamps();
        });

        Schema::create('user_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('author_id')->nullable();
            $table->unsignedInteger('editor_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('author_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('editor_id')->references('id')->on('users')->nullOnDelete();
            $table->string('title', 150);
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['user_id', 'is_pinned', 'created_at']);
            $table->index('created_at');
        });

        Schema::create('user_notes_note_tag', function (Blueprint $table) {
            $table->foreignId('note_id')->constrained('user_notes')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('user_notes_tags')->cascadeOnDelete();
            $table->primary(['note_id', 'tag_id']);
            $table->index('tag_id');
        });

        Schema::create('user_notes_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->constrained('user_notes')->cascadeOnDelete();
            $table->unsignedInteger('editor_id')->nullable();
            $table->foreign('editor_id')->references('id')->on('users')->nullOnDelete();
            $table->unsignedInteger('version');
            $table->string('action', 20);
            $table->string('title', 150);
            $table->text('content');
            $table->boolean('is_pinned');
            $table->json('tags');
            $table->timestamps();
            $table->unique(['note_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notes_revisions');
        Schema::dropIfExists('user_notes_note_tag');
        Schema::dropIfExists('user_notes');
        Schema::dropIfExists('user_notes_tags');
    }
};
