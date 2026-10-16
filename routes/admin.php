<?php

use Azuriom\Plugin\PlayerNotes\Controllers\Admin\NoteController;
use Azuriom\Plugin\PlayerNotes\Controllers\Admin\PlayerController;
use Azuriom\Plugin\PlayerNotes\Controllers\Admin\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [NoteController::class, 'index'])->name('index');
Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
Route::get('/players/{player}', [PlayerController::class, 'show'])->name('players.show');
Route::post('/players/{player}/notes', [NoteController::class, 'store'])
    ->middleware('can:player-notes.create')->name('notes.store');
Route::get('/players/{player}/notes/{note}/edit', [NoteController::class, 'edit'])->name('notes.edit');
Route::put('/players/{player}/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
Route::delete('/players/{player}/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
Route::patch('/players/{player}/notes/{note}/pin', [NoteController::class, 'pin'])
    ->middleware('can:player-notes.pin')->name('notes.pin');
Route::get('/players/{player}/notes/{note}/history', [NoteController::class, 'history'])->name('notes.history');
Route::resource('tags', TagController::class)->except(['show', 'create'])
    ->middleware('can:player-notes.tags');
