@if ($taxonomy->get('taxonomy') == 'tags')
    @include('cms::components.form.tags')
@elseif (in_array($taxonomy->get('taxonomy'), ['area', 'region']))

@elseif ($taxonomy->get('taxonomy') == 'attributes')
    @include('cms::components.form.attributes')
@else
    @include('cms::components.form.select')
@endif
