@if (isset($breadcrumbs))
    @php
        $itemListElement = [];
        $position = 1;

        $itemListElement[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => __('messages.home'),
            'item' => route('home'),
        ];
        if (!empty($breadcrumbs)) {
            foreach ($breadcrumbs as $breadcrumb) {
                $position++;
                $itemListElement[] = [
                    '@type' => 'ListItem',
                    'position' => $position,
                    'name' => $breadcrumb['title'],
                    'item' => $breadcrumb['path'],
                ];
            }
        }
        if (isset($main_post)) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $position + 1,
                'name' => $main_post->title,
                'item' => $main_post->getRoute(),
            ];
        }
        $schema = [
            '@context' => 'https://schema.org/',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement,
        ];
    @endphp
    <script type="application/ld+json" nonce="{{ csp_nonce() }}">{!! json_encode($schema, JSON_UNESCAPED_SLASHES) !!}</script>
@endif
