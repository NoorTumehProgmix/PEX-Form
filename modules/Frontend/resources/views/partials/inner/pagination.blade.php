@if (isset($data['meta']) && $data['meta']['last_page'] > 1)
    <ul class="pagination list-plain">
        @foreach ($data['meta']['links'] as $page)
            @php
                $url = $page['url'];

                if (!empty($url)) {
                    $parsedUrl = parse_url($url);
                    $query = [];

                    if (isset($parsedUrl['query'])) {
                        parse_str($parsedUrl['query'], $query);
                        if (isset($query['page']) && $query['page'] == 1) {
                            unset($query['page']);
                        }
                    }

                    $newQuery = http_build_query($query);

                    $url = $parsedUrl['path'] ?? '/';
                    if (!empty($newQuery)) {
                        $url .= '?' . $newQuery;
                    }
                }
            @endphp

            @if ($page['active'])
                <li>
                    <span class="page-link active">{{ $page['label'] }}</span>
                </li>
            @else
                <li>
                    <a href="{{ $url }}" class="page-link @if (empty($page['url']) || $url == '/') disabled @endif">
                        {{ $page['label'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
@endif
