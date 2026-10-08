<div class="form-group">
    @php
        $value = $value ?? [];
        $value = !is_array($value) ? [$value] : $value;
        $taxonomy = $taxonomy ?? '';
        $post_type = $post_type ?? '';

        $options = [];
       if ($value) {
    $query = \Juzaweb\Backend\Models\Taxonomy::query();

    if ($taxonomy) {
        $query->where('taxonomy', $taxonomy);
    }

    if ($post_type) {
        $query->where('post_type', $post_type);
    }

    $options = $query
        ->where('lang', app()->getLocale())
        ->get(['id', 'name'])
        ->mapWithKeys(function ($item) {
            return [
                $item->id => $item->name
            ];
        })
        ->toArray();
}
    @endphp
    <label class="col-form-label" for="{{ $id ?? $name }}">{{ $label ?? $name }}</label>
    <select name="{{ ($multiple ?? false) ? "{$name}[]" : $name }}" id="{{ $id ?? $name }}"
            class="form-control load-taxonomies-parent" data-post-type="{{ $post_type ?? '' }}"
            data-taxonomy="{{ $taxonomy ?? '' }}" {{ ($multiple ?? false) ? 'multiple' : '' }}>
        @foreach($options as $key => $tname)
            <option value="{{ $key }}" @if(in_array($key, $value)) selected @endif>{{ $tname }}</option>
        @endforeach
    </select>
</div>
