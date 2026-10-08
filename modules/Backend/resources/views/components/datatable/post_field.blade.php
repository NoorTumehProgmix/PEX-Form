<div class="form-group mb-2 mr-1">
    <select name="{{ $name }}" id="search-{{ $name }}" class="form-control load-posts" data-type="{{$name}}" data-placeholder="{{ trans_cms('cms::app.all') }} {{ $field['label'] }}">
        @if($field['selected'] != "")
         <option value="{{ $field['selected']['id'] }}">{{ $field['selected']['title'] }}</option>
        @endif
    </select>
</div>
