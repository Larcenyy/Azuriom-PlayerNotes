<input type="hidden" name="_note_form" value="1">
@if($note->exists)
    <input type="hidden" name="version" value="{{ old('version', $note->version) }}">
@endif
@error('version')
    <div class="alert alert-warning" role="alert">{{ $message }}</div>
@enderror
<div class="mb-3">
    <label class="form-label" for="noteTitle">{{ trans('user-notes::admin.note_title') }}</label>
    <input type="text" id="noteTitle" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $note->title) }}" maxlength="150" required>
    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="noteContent">{{ trans('user-notes::admin.content') }}</label>
    <textarea id="noteContent" name="content" class="form-control @error('content') is-invalid @enderror" rows="7" maxlength="10000" aria-describedby="contentHelp" required>{{ old('content', $note->content) }}</textarea>
    @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
    <div id="contentHelp" class="form-text">{{ trans('user-notes::admin.content_hint') }}</div>
</div>
<fieldset class="mb-3">
    <legend class="fs-6">{{ trans('user-notes::admin.tags') }}</legend>
    <p class="form-text">{{ trans('user-notes::admin.tags_hint') }}</p>
    <div class="d-flex flex-wrap gap-3">
        @forelse($tags as $tag)
            <div class="form-check">
                <input type="checkbox" id="noteTag{{ $tag->id }}" name="tags[]" value="{{ $tag->id }}" class="form-check-input" @checked(in_array($tag->id, old('_note_form') ? old('tags', []) : ($note->exists ? $note->tags->modelKeys() : [])))>
                <label class="form-check-label" for="noteTag{{ $tag->id }}">@include('user-notes::admin._tag')</label>
            </div>
        @empty
            <span class="text-muted">{{ trans('user-notes::admin.no_tags_hint') }}</span>
        @endforelse
    </div>
    @error('tags') <div class="text-danger">{{ $message }}</div> @enderror
    @error('tags.*') <div class="text-danger">{{ $message }}</div> @enderror
</fieldset>
<button type="submit" class="btn btn-primary"><i class="bi bi-save" aria-hidden="true"></i> {{ trans('user-notes::admin.save') }}</button>
