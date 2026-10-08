<?php

/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/juzacms
 * @author     The Anh Dang
 * @link       https://juzaweb.com/cms
 * @license    GNU V2
 */

namespace Juzaweb\Frontend\Providers;

use Illuminate\Support\Facades\Cache;
use Juzaweb\Backend\Models\Menu;
use Juzaweb\Backend\Models\MenuItem;
use Juzaweb\CMS\Facades\Plugin;
use Juzaweb\CMS\Support\ServiceProvider;
use Juzaweb\Backend\Models\Post;
use Jenssegers\Agent\Agent;

class FrontendServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Share pages data with 'frontend::layouts.app' view
        view()->composer('frontend::layouts.app', function ($view) {
            $header_menu = jw_nav_menu([
                'container_before' => '<ul class="nav-links" role="list">',
                'container_after' => '</ul>',
                'theme_location' => 'header_menu',
                'item_view' => 'frontend::partials.menu.menu_item',
            ]);

            $header_mobile_menu = jw_nav_menu([
                'container_before' => '',
                'container_after' => '',
                'theme_location' => 'header_menu',
                'item_view' => 'frontend::partials.menu.mobile_menu',
            ]);

            $footer_menu = jw_nav_menu([
                'container_before' => '',
                'container_after' => '',
                'theme_location' => 'footer_menu',
                'item_view' => 'frontend::partials.menu.footer_menu',
            ]);

            $sub_footer = jw_nav_menu([
                'container_before' => '<ul class="footer-links" role="list">',
                'container_after' => '</ul>',
                'theme_location' => 'sub_footer',
                'item_view' => 'frontend::partials.menu.sub_footer',
            ]);

            $fbAppId = get_config('fb_app_id');
            $googleAnalytics = get_config('google_analytics');
            $bingKey = get_config('bing_verify_key');
            $googleKey = get_config('google_verify_key');
            $facebookPixelId = get_config('facebook_pixel_id');

            //get search quick links
            $plugins = Plugin::all();
            $quickLinks = null;
            if (pluginActive("progmix/search-log")) {
                $menu = Menu::where('type', 'quick-links')->where('lang', app()->getLocale())->first();

                $quickLinks = jw_nav_menu([
                    'menu' => @$menu->id,
                    'container_before' => '',
                    'container_after' => '',
                    'theme_location' => '',
                    'item_view' => 'frontend::partials.menu.quick_links',
                ]);
            }

            $view->with(compact('fbAppId', 'googleAnalytics', 'bingKey', 'googleKey', 'header_menu', 'header_mobile_menu', 'footer_menu', 'quickLinks', 'sub_footer', 'facebookPixelId'));
        });

        // Share locales data with 'frontend::partials.language_switcher' view
        view()->composer('frontend::partials.language_switcher', function ($view) {
            $view->with('locales', config('app.locales'));
        });

        // Share current locale data with all views
        view()->composer('*', function ($view) {
            $view->with('current_locale', app()->getLocale());
            $agent = new Agent();
            $view->with('agent', $agent);
        });
    }

    public function register()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'frontend');
    }
}
