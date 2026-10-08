<div class="form-group">
    @php
        $value = $value ?? [];
        $value = !is_array($value) ? [$value] : $value;
        $options = [];

        if ($value) {
            $query = app($table)->query();
            $query->select('id', 'title');
            $where =
                count($value) == 1
                    ? ['id' => $value[0]] // single id
                    : ['id_in' => $value]; // multiple ids

            $result = $query->whereIn('id', $value)->get();

            $options = $result
                ->mapWithKeys(function ($item) {
                    return [
                        $item->id => $item->title,
                    ];
                })
                ->toArray();
        }
    @endphp
    <label class="col-form-label" for="{{ $id ?? $name }}">{{ $label ?? $name }}</label>
    <select name="{{ $multiple ?? false ? "{$name}[]" : $name }}" id="{{ $id ?? $name }}" class="form-control load-table"
        data-table="{{ $table ?? '' }}" data-title-field="{{ $title_field ?? 'title' }}"
        {{ $multiple ?? false ? 'multiple' : '' }}>
        @foreach ($options as $key => $tname)
            <option value="{{ $key }}" @if (in_array($key, $value)) selected @endif>{{ $tname }}
            </option>
        @endforeach
    </select>
</div>
