@foreach ($items as $item)
    <div class="col-md-3 col-sm-3 col-6 d-flex">
        <div class="footer-item footer-item-mobile">
            <div class="footer-item--title">{!! $item['label'] !!}</div>
            <div class="footer-item-content">
                @if (isset($item['children']) and !empty($item['children']))
                    <ul class="footer-list list-plain">
                        @foreach ($item['children'] as $child)
                            <li>
                                @if (!empty($child['link']) and $child['link'] != '#')
                                    <a href="{{ $child['link'] }}" title="{{ $child['label'] }}"
                                        class="item">{{ $child['label'] }}</a>
                                @else
                                    <span class="item">{{ $child['label'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
    @if ($loop->index === 3)
        @break
    @endif
@endforeach
