@foreach ($items as $item)
    @if (!empty($item['link']) && $item['link'] != '#')
        <a href="{{ $item['link'] }}" {!! hrefAttr($item['link']) !!}
            @if (isset($item['target']) && $item['target'] == '_blank') target="_blank" @endif>{{ $item['label'] }}</a>
    @else
        <span>{{ $item['label'] }}</span>
    @endif
@endforeach