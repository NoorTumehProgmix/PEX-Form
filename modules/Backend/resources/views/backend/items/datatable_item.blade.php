@php
    if (isset($editUrlShow) && $editUrlShow === false) {
        unset($actions['edit']);
    }
@endphp
<div class="font-weight-bold">
    <a href="{{ $editUrl }}">{{ $value }}</a>
</div>
