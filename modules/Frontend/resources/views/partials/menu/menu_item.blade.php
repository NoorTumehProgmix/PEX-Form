@php
    $level = isset($level) ? $level : 'top';
@endphp

@foreach ($items as $item)
    @php
        $li_class = '';

        $is_current = false;
        if (isset($item['link'])) {
            $is_current = parse_url(Request::url(), PHP_URL_PATH) == parse_url(url($item['link']), PHP_URL_PATH);
        }
        $is_home = Request::url() == url('/');
        if ($is_current || ($is_home && $item['link'] == '/')) {
            $li_class = 'active';
        }
        if (isset($item['children']) and !empty($item['children'])) {
            $li_class .= ' has-dropdown';
        }
    @endphp

    <li @if (!empty($li_class)) class="{{ $li_class }}" @endif>
        @if (!empty($item['link']) && $item['link'] != '#')
            <a href="{{ $item['link'] }}" {!! hrefAttr($item['link']) !!}
                @if (isset($item['target']) && $item['target'] == '_blank') target="_blank" @endif class="item">{{ $item['label'] }}</a>
        @else
            <span class="item">{{ $item['label'] }}</span>
        @endif
        @if (isset($item['children']) and !empty($item['children']))
            <ul class="dropdown-menu list-plain">
                @foreach ($item['children'] as $child)
                    @include('frontend::partials.menu.menu_item', ['items' => [$child], 'level' => 'sub'])
                @endforeach
            </ul>
        @endif
    </li>
@endforeach
