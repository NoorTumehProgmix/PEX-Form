<div class="form-group mb-2 mr-1  @if(isset($field['hidden']) && $field['hidden']) d-none @endif">
    <select name="{{ $name }}" id="search-{{ $name }}" class="form-control select2-default" data-width="{{ $field['width'] ?? '100%' }}" >
        <option value="">{{ trans_cms('cms::app.all') }} {{ $field['label'] }}</option>
        @foreach($field['options'] ?? [] as $key => $val)
        <option value="{{ $key }}" @if(isset($field['selected']) && $field['selected'] == $key) selected @endif>{{ $val }}</option>
        @endforeach
    </select>
</div>
