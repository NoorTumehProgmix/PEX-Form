<div class="form-group mb-2 mr-1">
    <label for="search-{{ $name }}" class="sr-only">{{ $field['label'] ?? '' }}</label>
    <input name="{{ $name }}" type="{{ isset($field['sub_type']) ? $field['sub_type'] : 'text' }}" id="search-{{ $name }}" class="form-control" placeholder="{{ $field['placeholder'] ?? '' }}" autocomplete="off">
</div>
