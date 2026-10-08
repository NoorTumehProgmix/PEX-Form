@php
    $socialJson = json_encode(get_schema_social_urls(), JSON_UNESCAPED_SLASHES);
    $schema = [
        '@context' => 'https://schema.org/',
        '@type' => 'Organization',
        'name' => get_config("title_$current_locale") . ' | ' . get_config("sitename_$current_locale"),
        'url' => route('home'),
        'logo' => url(get_logo()),
        'description' => clean_schema_description(get_config("description_$current_locale")),
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => get_config('phone'),
            'contactType' => 'customer service',
        ],
        'sameAs' => json_decode($socialJson),
    ];
@endphp
<script type="application/ld+json" nonce="{{ csp_nonce() }}">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
