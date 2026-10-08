@if($row->active)
    <span class="text-success">{{ trans_cms('cms::app.active') }}</span>
@else
    <span class="text-secondary">{{ trans_cms('cms::app.inactive') }}</span>
@endif