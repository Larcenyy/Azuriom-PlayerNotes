<div class="mb-3">
    <label class="form-label" for="tagName">{{ trans('user-notes::admin.tag_name') }}</label>
    <input class="form-control @error('name') is-invalid @enderror" type="text" id="tagName" name="name" value="{{ old('name', $tag->name) }}" maxlength="50" required>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="tagColor">{{ trans('user-notes::admin.tag_color') }}</label>
    <select class="form-select @error('color') is-invalid @enderror" id="tagColor" name="color" required>
        @foreach(\Azuriom\Plugin\UserNotes\Models\Tag::COLORS as $color)
            <option value="{{ $color }}" @selected(old('color', $tag->color) === $color)>{{ trans('user-notes::admin.colors.'.$color) }}</option>
        @endforeach
    </select>
    @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<button class="btn btn-primary" type="submit">{{ trans('user-notes::admin.save') }}</button>
