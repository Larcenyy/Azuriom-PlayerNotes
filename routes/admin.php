<?php

use Azuriom\Plugin\UserNotes\Controllers\Admin\NoteController;
use Azuriom\Plugin\UserNotes\Controllers\Admin\UserController;
use Azuriom\Plugin\UserNotes\Controllers\Admin\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [NoteController::class, 'index'])->name('index');
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::post('/users/{user}/notes', [NoteController::class, 'store'])
    ->middleware('can:user-notes.create')->name('notes.store');
Route::get('/users/{user}/notes/{note}/edit', [NoteController::class, 'edit'])->name('notes.edit');
Route::put('/users/{user}/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
Route::delete('/users/{user}/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
Route::patch('/users/{user}/notes/{note}/pin', [NoteController::class, 'pin'])
    ->middleware('can:user-notes.pin')->name('notes.pin');
Route::get('/users/{user}/notes/{note}/history', [NoteController::class, 'history'])->name('notes.history');
Route::resource('tags', TagController::class)->except(['show', 'create'])
    ->middleware('can:user-notes.tags');
