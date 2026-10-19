<form method="GET" action="{{ $filterAction }}" class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-6">
                <label class="form-label" for="searchInput">{{ trans('user-notes::admin.search') }}</label>
                <input class="form-control" id="searchInput" type="search" name="search" maxlength="150" value="{{ request('search') }}" placeholder="{{ trans('user-notes::admin.search_notes') }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label" for="tagFilter">{{ trans('user-notes::admin.tags') }}</label>
                <select class="form-select" id="tagFilter" name="tag">
                    <option value="">{{ trans('user-notes::admin.all_tags') }}</option>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" @selected((string) request('tag') === (string) $tag->id)>{{ $tag->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label" for="authorFilter">{{ trans('user-notes::admin.author') }}</label>
                <select class="form-select" id="authorFilter" name="author">
                    <option value="">{{ trans('user-notes::admin.all_authors') }}</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}" @selected((string) request('author') === (string) $author->id)>{{ $author->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex flex-wrap gap-3 align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="pinnedFilter" name="pinned" value="1" @checked(request('pinned') === '1')>
                    <label class="form-check-label" for="pinnedFilter">{{ trans('user-notes::admin.pinned_only') }}</label>
                </div>
                <button type="submit" class="btn btn-primary">{{ trans('user-notes::admin.filter') }}</button>
                <a class="btn btn-outline-secondary" href="{{ $filterAction }}">{{ trans('user-notes::admin.reset') }}</a>
            </div>
        </div>
    </div>
</form>
