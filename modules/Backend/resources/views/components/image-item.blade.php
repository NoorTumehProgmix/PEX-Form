<div class="image-item border">
    <a href="javascript:void(0)" class="text-danger remove-image-item">
        <i class="fa fa-trash"></i>
    </a>
    <a href="javascript:void(0)" class="text-secondary drag-handle"
        style="position: absolute; top: 0; left: 5px; cursor: move; z-index: 10;">
        <i class="fa fa-arrows"></i>
    </a>
    @php
        $filename = pathinfo($url, PATHINFO_BASENAME);
        if ($icon == 'fa-youtube-play') {
            $filename = $url;
        }
    @endphp

    @if ($icon == 'fa-file-image-o')
        <div title="{{ $filename }}" class="image-preview" style="background-image: url({{ $url }});"></div>
    @else
        <div title="{{ $filename }}" class="image-preview"></div>
    @endif
    @if ($icon != '' && $icon != 'fa-file-image-o')
        <span title="{{ $filename }}" class="fa {{ $icon }}"
            style="font-size: 42px;
    position: absolute;
    top: 26%;"></span>
    @endif
    <span title="{{ $filename }}"
        style="white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;">{{ $filename }}</span>
    <input type="hidden" name="{{ $name }}" class="input-path" value="{{ $path }}">
</div>
