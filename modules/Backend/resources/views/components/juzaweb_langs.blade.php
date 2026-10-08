@php
    $langs = array_merge(trans_cms('cms::app', [], 'en'), trans_cms('cms::app'));
    $plugins = \Juzaweb\CMS\Facades\Plugin::all(true)
        ->map(fn($item) => Arr::only($item, ['name', 'description']))
        ->values();
    $themes = [
    [
        "name" => "default",
        "title" => "Default",
        "description" => "Default Theme",
        "version" => "1.0.2",
        "active" => true,
    ]
];
@endphp
<script nonce="{{ csp_nonce() }}" type="text/javascript">
    /**
     * JUZAWEB CMS - THE BEST CMS FOR LARAVEL PROJECT
     *
     * @package    juzaweb/juzacms
     * @link       https://juzaweb.com
     * @license    GNU V2
     */
    const juzaweb = {
        adminPrefix: "{{ config('juzaweb.admin_prefix') }}",
        adminUrl: "{{ url(config('juzaweb.admin_prefix')) }}",
        lang: @json($langs),
        plugins: @json($plugins),
        themes: @json($themes)
    }
</script>
