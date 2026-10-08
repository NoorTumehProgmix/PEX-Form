@foreach ($items as $item)
    <li>
        <a href="{{ $item['link'] }}" {!! hrefAttr($item['link']) !!} title="{{ $item['label'] }}" class="item">
            {{ $item['label'] }}
        </a>
    </li>
@endforeach
