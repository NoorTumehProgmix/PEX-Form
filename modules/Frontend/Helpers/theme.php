<?php

/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/juzacms
 * @author     The Anh Dang
 * @link       https://juzaweb.com/cms
 * @license    GNU V2
 */

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Juzaweb\Backend\Http\Resources\CommentResource;
use Juzaweb\Backend\Models\Comment;
use Juzaweb\Backend\Models\MediaFile;
use Juzaweb\Backend\Models\Menu;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\CMS\Facades\Plugin;
use Juzaweb\CMS\Facades\ThemeConfig;
use Juzaweb\CMS\Facades\ThemeLoader;
use Juzaweb\CMS\Support\Theme\BackendMenuBuilder;
use Juzaweb\CMS\Support\Theme\MenuBuilder;
use TwigBridge\Facade\Twig;
use Spatie\TranslationLoader\LanguageLine;

function body_class($class = '')
{
    $class = trim('jw-theme jw-theme-body ' . $class);

    return apply_filters('theme.body_class', $class);
}

function theme_assets(string $path, ?string $theme = null): ?string
{
    return ThemeLoader::assets($path, $theme);
}

function theme_asset(string $path, ?string $theme = null): ?string
{
    return ThemeLoader::assets($path, $theme);
}

function plugin_asset(string $path, ?string $plugin = null): ?string
{
    return Plugin::assets($plugin, $path);
}

function plugin_assets(string $path, ?string $plugin = null): ?string
{
    return Plugin::assets($plugin, $path);
}

if (!function_exists('page_url')) {
    function page_url($slug): string
    {
        return url()->to($slug);
    }
}

/**
 * Get particular theme all information.
 *
 * @param string $theme
 * @param string $path
 * @return string
 */
function theme_path(string $theme, string $path = ''): string
{
    return ThemeLoader::getThemePath($theme, $path);
}

if (!file_exists('jw_theme_info')) {
    /**
     * Get particular theme all information.
     *
     * @param string|null $theme
     * @return null|\Noodlehaus\Config|Collection
     */
    function jw_theme_info(?string $theme = null): Collection|\Noodlehaus\Config|null
    {
        if (empty($theme)) {
            return jw_theme_info(jw_current_theme());
        }

        return ThemeLoader::getThemeInfo($theme);
    }
}

if (!function_exists('jw_current_theme')) {
    /**
     * Get current active theme
     *
     * @return string
     */
    function jw_current_theme(): string
    {
        $theme = get_config('theme_statuses', []);
        return Arr::get($theme, 'name', 'default');
    }
}

if (!function_exists('jw_theme_config')) {
    /**
     * Get particular theme all information.
     *
     * @param string|null $theme
     * @return Collection
     */
    function jw_theme_config(?string $theme = null): Collection
    {
        if (empty($theme)) {
            $theme = jw_current_theme();
        }

        return ThemeLoader::getThemeConfig($theme);
    }
}

if (!function_exists('jw_home_page')) {
    function jw_home_page()
    {
        return apply_filters('get_home_page', get_config('home_page'));
    }
}

if (!function_exists('get_name_template_part')) {
    /**
     * Get template part name.
     *
     * @param string $type // Singular of post
     * @param string $slug
     * @param ?string $name
     * @return string
     */
    function get_name_template_part(string $type, string $slug, ?string $name = null): string
    {
        $name = (string)$name;

        if ($name !== '') {
            $template = "{$slug}-{$name}";
            if (view()->exists(theme_viewname("theme::template-parts.{$template}"))) {
                return $template;
            }
        }

        if ($type != 'post') {
            $template = "{$slug}-{$type}";
            if (view()->exists(theme_viewname("theme::template-parts.{$template}"))) {
                return $template;
            }
        }

        return $slug;
    }
}

if (!function_exists('jw_menu_items')) {
    /**
     * Get menu item in menu
     *
     * @param Menu $menu
     * @return Collection
     */
    function jw_menu_items(Menu $menu): Collection
    {

        $menuItems = $menu->items()
            ->orderBy('num_order', 'ASC')
            ->get();

        // Filter out the deleted items from the collection
        $menuItems = $menuItems->filter(function ($item) {
            return $item->isNotDeleted();
        });

        return $menuItems;
    }
}

if (!function_exists('jw_menu_items_cached')) {
    /**
     * Get menu item in menu
     *
     * @param Menu $menu
     * @return Collection
     */
    function jw_menu_items_cached(Menu $menu): Collection
    {

        $cacheTime = get_config('cache_duration', 0);
        $menuItems = Cache::remember("menuitems-$menu-" . app()->getLocale(), $cacheTime, function () use ($menu) {
            return $menu->items()
                ->orderBy('num_order', 'ASC')
                ->get();
        });

        // Filter out the deleted items from the collection
        $filteredMenuItems = Cache::remember("filtered_menu_items-$menu" . app()->getLocale(), $cacheTime, function () use ($menuItems) {
            return $menuItems->filter(function ($item) {
                return $item->isNotDeleted();
            });
        });

        return $filteredMenuItems;
    }
}

if (!function_exists('jw_page_menu')) {
    function jw_page_menu(?array $args): string
    {
        return trans_cms('cms::app.menu_not_found');
    }
}

if (!function_exists('jw_nav_menu')) {
    function jw_nav_menu(?array $args = [])
    {
        $defaults = [
            'menu'             => '',
            'container_before' => '',
            'container_after'  => '',
            'fallback_cb'      => 'jw_page_menu',
            'theme_location'   => '',
            'item_view'        => 'frontend::partials.menu_item',
        ];

        $args = array_merge($defaults, $args);
        $menu = null;
        if ($args['theme_location']) {
            $menu = get_menu_by_theme_location($args['theme_location']);
        }

        if ($args['menu']) {
            $menu = $args['menu'];
        }

        if (is_numeric($menu)) {
            $cacheTime = get_config('cache_duration', 0);
            $location  = $args['theme_location'];
            $lang      = app()->getLocale();

            $menu = cache()->remember("menu-$menu-$location-$lang", $cacheTime, function () use ($menu) {
                return Menu::find($menu);
            });
        }

        if (empty($menu)) {
            //return call_user_func($args['fallback_cb'], $args);
            return "";
        }

        $items   = jw_menu_items_cached($menu);
        $builder = new MenuBuilder($items, $args);
        return $builder->render();
    }
}

if (!function_exists('jw_nav_backend_menu')) {
    function jw_nav_backend_menu($args = [])
    {
        $defaults = [
            'menu'             => '',
            'container_before' => '',
            'container_after'  => '',
            'fallback_cb'      => 'jw_page_menu',
            'theme_location'   => '',
            'item_view'        => 'cms::items.menu_item',
        ];

        $args = array_merge($defaults, $args);
        if (is_string($args['item_view'])) {
            $args['item_view'] = view($args['item_view']);
        }

        $menu = null;

        if ($args['menu']) {
            $menu = $args['menu'];
        }

        if ($args['theme_location']) {
            $menu = get_menu_by_theme_location($args['theme_location']);
            $menu = Menu::find($menu);
        }

        if (empty($menu)) {
            return call_user_func($args['fallback_cb'], $args);
        }

        $items   = jw_menu_items($menu);
        $builder = new BackendMenuBuilder($items, $args);

        return $builder->render();
    }
}

if (!function_exists('set_theme_config')) {
    function set_theme_config($key, $value)
    {
        return ThemeConfig::setConfig($key, $value);
    }
}

if (!function_exists('get_theme_config')) {
    function get_theme_config($key, $default = null): null|array|string
    {
        return ThemeConfig::getConfig($key, $default);
    }
}

if (!function_exists('get_theme_mod')) {
    function get_theme_mod($key, $default = null): array|string
    {
        return ThemeConfig::getConfig($key, $default);
    }
}

if (!file_exists('get_menu_by_theme_location')) {
    function get_menu_by_theme_location($location)
    {
        $lang      = app()->getLocale();
        $locations = get_theme_config("nav_location_$lang");
        $menuId    = $locations[$location] ?? null;
        if ($menuId) {
            return $menuId;
        }

        return null;
    }
}

if (!function_exists('embed_url')) {
    function embed_url($url = null): string
    {
        $queryString = parse_url($url, PHP_URL_QUERY);
        parse_str($queryString, $parameters);
        if (isset($parameters['v'])) {
            return 'https://www.youtube.com/embed/' . $parameters['v'];
        }
        return "";
    }
}
if (!function_exists('check_link')) {
    function check_link($post)
    {
        if ($post->external_link != "") {
            return redirect($post->external_link, 301);
        }
        return "";
    }
}

if (!function_exists('path_link')) {
    function path_link($path = null): string
    {
        return ltrim($path, '/');
    }
}
if (!function_exists('disabled_or_link')) {
    function disabled_or_link($post_template, $path): string
    {
        return $post_template == 'disable' ? 'javascript:void(0)' : route('post', path_link($path));
    }
}
if (!function_exists('format_date')) {
    function format_date($post_date, $date_format = null): array
    {
        if (is_null($post_date)) {
            return [
                'date' => "",
                'time' => "",
            ];
        }
        // Set the locale to the app's current language
        Carbon::setLocale(app()->getLocale());
        $date       = Carbon::createFromFormat('Y-m-d H:i:s', $post_date);
        $DateFormat = get_config('date_format') == 'custom' ? get_config('date_format_custom') : get_config('date_format');
        $DateFormat = $date_format ? $date_format : $DateFormat;
        $TimeFormat = get_config('time_format') == 'custom' ? get_config('time_format_custom') : get_config('time_format');

        return [
            'date' => Carbon::parse($post_date)->translatedFormat($DateFormat),
            'time' => Carbon::parse($post_date)->translatedFormat($TimeFormat),
        ];
    }
}

if (!function_exists('get_icon')) {
    function get_icon($default = null): string
    {
        return upload_url(
            get_config('icon'),
            asset($default ?: 'default/images/favicon.ico')
        );
    }
}
if (!function_exists('get_theme_color')) {
    function get_theme_color($color_id): string
    {
        $themeColors = get_config('theme_colors');
        if (isset($themeColors[$color_id])) {
            return $themeColors[$color_id];
        }
        return $color_id;
    }
}
if (!function_exists('parse_editor_content')) {
    function parse_editor_content($content): string
    {
        $themeColors = get_config('theme_colors');
        foreach ($themeColors as $key => $color) {
            $content = str_replace('color: ' . $key, 'color: ' . $color, $content);
            $content = str_replace('background-color: ' . $key, 'background-color: ' . $color, $content);
        }

        // Update image URLs
        $storageServerUrl = config('filesystems.disks.storage_server.base_url');
        // Match <img> tags with relative src attribute
        $pattern = '/<(?:img|iframe|video)[^>]*(?:src)=\"(?!http)(.*?)\"[^>]*>/i';
        preg_match_all($pattern, $content, $matches);

        // Iterate over matched images and update their src attributes
        foreach ($matches[1] as $src) {
            if (strpos($src, 'storage') !== 0 && strpos($src, '/storage') !== 0) {
                $updatedSrc = $storageServerUrl . '/storage/' . $src;
            } else {
                $updatedSrc = $storageServerUrl . '/' . $src;
            }
            $content = str_replace($src, $updatedSrc, $content);
        }

        $content = preg_replace('/\s*style\s*=\s*"[^"]*"/i', '', $content);
        $content = str_replace('##nonce_function_name##', csp_nonce(), $content);

        // Apply video link detection
        $content = markVideoLinks($content);

        return $content;
    }
}

if (!function_exists('markVideoLinks')) {
    function markVideoLinks(string $html)
    {
        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        $links = $doc->getElementsByTagName('a');

        // Video file extensions
        $videoExtensions = ['mp4', 'webm', 'ogg'];
        // Video domains
        $videoDomains = ['youtube.com', 'youtu.be', 'vimeo.com'];

        foreach ($links as $link) {
            $href = strtolower($link->getAttribute('href'));
            $isVideo = false;

            // Check by extension
            foreach ($videoExtensions as $ext) {
                if (str_ends_with($href, '.' . $ext)) {
                    $isVideo = true;
                    break;
                }
            }

            // Check by domain
            if (!$isVideo) {
                foreach ($videoDomains as $domain) {
                    if (strpos($href, $domain) !== false) {
                        $isVideo = true;
                        break;
                    }
                }
            }

            // If it’s a video link → add class
            if ($isVideo) {
                $existingClass = $link->getAttribute('class');
                $link->setAttribute('class', trim($existingClass . ' isvideo'));
            }
        }

        // Extract only <body> content (avoid full HTML structure)
        $body = $doc->getElementsByTagName('body')->item(0);
        if ($body) {
            $html = $doc->saveHTML($body);
            // Remove <body> tags
            $html = preg_replace('~^<body>|</body>$~i', '', $html);
        }

        return $html;
    }
}

if (!function_exists('is_home')) {
    function is_home(): bool
    {
        return Route::currentRouteName() == 'home';
    }
}

if (!function_exists('jw_get_sidebar')) {
    function jw_get_sidebar($key): Collection
    {
        return HookAction::getSidebars($key);
    }
}

if (!function_exists('jw_get_widgets_sidebar')) {
    function jw_get_widgets_sidebar($key): Collection
    {
        $content = get_theme_config('sidebar_' . $key, []);

        return collect($content);
    }
}

if (!function_exists('dynamic_sidebar')) {
    function dynamic_sidebar($key)
    {
        $sidebar = HookAction::getSidebars($key);
        if (empty($sidebar)) {
            return '';
        }

        $widgets = jw_get_widgets_sidebar($key);

        return view(
            'cms::components.dynamic_sidebar',
            compact(
                'widgets',
                'sidebar'
            )
        );
    }
}

if (!function_exists('dynamic_block')) {
    function dynamic_block($post, $key)
    {
        $data = $post['metas']['block_content'][$key] ?? [];
        $keys = collect($data)->pluck('block')->toArray();

        $blocks = HookAction::getPageBlocks()->filter(
            function ($item) use ($keys) {
                return in_array($item->get('key'), $keys);
            }
        );

        return view(
            'cms::components.dynamic_block',
            compact(
                'data',
                'blocks'
            )
        );
    }
}

if (!function_exists('installed_themes')) {
    function installed_themes(): array
    {
        $themes = ThemeLoader::all();

        return array_keys($themes);
    }
}

if (!function_exists('get_template_view')) {
    function get_template_view($template_name): string
    {
        return ThemeLoader::getRegister(jw_current_theme(), 'templates')[$template_name]['view'];
    }
}
if (!function_exists('get_landing_page_view')) {
    function get_landing_page_view($template_name): string
    {
        return ThemeLoader::getRegister(jw_current_theme(), 'landing_pages')[$template_name]['view'];
    }
}

if (!function_exists('get_template_layout')) {
    function get_template_layout($template_name): string
    {
        $templates = ThemeLoader::getRegister(jw_current_theme(), 'templates');
        return isset($templates[$template_name]['layout']) ? $templates[$template_name]['layout'] : '';
    }
}

if (!function_exists('get_template_view_name')) {
    function get_template_view_name($template_name): string
    {
        $view          = ThemeLoader::getRegister(jw_current_theme(), 'templates')[$template_name]['view'];
        $template_name = explode("::", $view);
        return end($template_name);
    }
}

if (!function_exists('comment_template')) {
    /**
     * Show comments frontend
     *
     * @param \Juzaweb\CMS\Traits\PostTypeModel $post
     * @param string|null $view
     * @return void
     */
    function comment_template($post, ?string $view = null): void
    {
        if (empty($view)) {
            $view = 'cms::items.frontend_comment';
        }

        $rows = Comment::with(['user'])
            ->where('object_id', '=', $post['id'])
            ->whereApproved()
            ->paginate(10);

        $comments = CommentResource::collection($rows)
            ->response()
            ->getData(true);
        $total    = $rows->total();

        Twig::display(
            $view,
            compact(
                'comments',
                'total'
            )
        );
    }
}

function theme_header(): void
{
    do_action('theme.header');
}

function theme_footer(): void
{
    do_action('theme.footer');
}

function theme_after_body(): void
{
    do_action('theme.after_body');
}

function theme_action($action): void
{
    if (
        in_array(
            $action,
            [
                'auth_form',
            ]
        )
    ) {
        do_action($action);
    }
}

/**
 * Loads a template part into a template.
 *
 * @param array $post
 * @param string $slug
 * @param string $name
 * @param array $args
 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
 */
function get_template_part($post, $slug, $name = null, $args = [])
{
    do_action("get_template_part_{$slug}", $post, $slug, $name, $args);

    $post     = (array)$post;
    $name     = (string)$name;
    $type     = $post ? Str::singular($post['type']) : 'none';
    $template = get_name_template_part($type, $slug, $name);

    return Twig::display(
        'theme::template-parts.' . $template,
        [
            'post' => $post,
        ]
    );
}

function paginate_links($data, $view = null, $params = [])
{
    if (empty($view)) {
        $view = 'frontend::partials.pagination';
    }
    $queryParams = request()->only(['date', 'category', 'tag', 'keyword']);
    if (!empty($queryParams)) {
        $data['meta']['links'] = collect($data['meta']['links'])->map(function ($page) use ($queryParams) {
            if (!is_null($page['url'])) {
                $page['url'] = $page['url'] . '&' . http_build_query($queryParams);
            } else {
                $page['url'] = "/";
            }
            return $page;
        })->toArray();
    }

    return view($view, compact('data'));
}

function theme_viewname($name)
{
    return $name;
}

function comment_form($post, $view = 'cms::comment_form')
{
    return Twig::display(
        $view,
        compact(
            'post'
        )
    );
}

function get_locale(): string
{
    return app()->getLocale();
}

function home_url(): string
{
    return '/';
}


function share_url($social, $url, $text = null): string
{
    $url  = urlencode($url);
    $text = urlencode($text);

    return match ($social) {
        'facebook' => "https://www.facebook.com/sharer.php?u={$url}",
        'twitter' => "https://twitter.com/intent/tweet?url={$url}&text={$text}",
        'telegram' => "https://t.me/share/url?url={$url}&text={$text}",
        'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$url}",
        'pinterest' => "https://pinterest.com/pin/create/button/?url={$url}&description={$text}",
        default => '',
    };
}

if (!function_exists('get_image_meta')) {
    function get_image_meta($imageUrl, $title = "")
    {
        $cacheKey = 'image_meta:' . md5($imageUrl . $title) . '-' . app()->getLocale();
        return Cache::remember($cacheKey, get_config('cache_duration', 0), function () use ($imageUrl, $title) {
            $meta      = [
                'caption' => "",
                'alt'     => $title,
            ];
            $imageUrl  = str_replace('/storage/', '', $imageUrl);
            $mediaFile = MediaFile::where('path', $imageUrl)->first();
            if ($mediaFile) {
                $imageMeta = $mediaFile->mediaMeta()->first();
                if ($imageMeta) {
                    $meta = [
                        'caption' => $imageMeta->caption,
                        'alt'     => $imageMeta->text,
                    ];
                }
            }

            return $meta;
        });
    }
}

if (!function_exists('get_og_default')) {
    function get_og_default($default = null): ?string
    {
        $current_locale = get_locale();
        return upload_url(get_config("og_image_default_$current_locale"), asset($default ?: 'assets/images/og-image.png'), size: '1200x630', quality: 100, webp: false);
    }
}

if (!function_exists('get_default_thumbnail')) {
    function get_default_thumbnail($default = null): ?string
    {
        $current_locale = get_locale();
        return upload_url(get_config("thumbnail_default_$current_locale"), asset($default ?: 'assets/images/thumb-default.webp'), size: '1080x1080', quality: 100, webp: true);
    }
}


if (!function_exists('getYoutubeImage')) {
    function getYoutubeImage($url): string
    {
        return 'https://img.youtube.com/vi/' . get_youtube_id($url) . '/hqdefault.jpg';
    }
}

if (!function_exists('DBTrans')) {
    function DBTrans($label): string
    {

        $split = explode('.', $label);
        if (isset($split[0]) && isset($split[1])) {
            $translationRecord = LanguageLine::where('namespace', $split[0])
                ->where('key', $split[1])->first();
            if ($translationRecord != null) {

                $translations = $translationRecord->text;
                if (is_array($translations) && isset($translations[app()->getLocale()])) {
                    return $translations[app()->getLocale()];
                } elseif (is_string($translations)) {
                    $translationArray = json_decode($translations, true);
                    if (isset($translationArray[app()->getLocale()])) {
                        return $translationArray[app()->getLocale()];
                    } else {
                        return $label;
                    }
                } else {
                    return $label;
                }
            } else {
                return $label;
            }
        } else {
            return $label;
        }
    }
}
