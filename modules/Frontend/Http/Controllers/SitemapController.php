<?php

namespace Juzaweb\Frontend\Http\Controllers;

use Illuminate\Support\Facades\App;
use Juzaweb\Backend\Models\Post;
use Juzaweb\CMS\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;


class SitemapController extends Controller
{
    protected $cacheTime;

    public function __construct()
    {
        if (!get_config('jw_enable_sitemap', true)) {
            abort(404);
        }
    }

    public function index($lang)
    {
        $supportedLanguages = config('app.locales');
        if (array_key_exists($lang, $supportedLanguages)) {
            $sitemap = App::make("sitemap");
            $sitemap->setCache(cache_prefix("sitemap-$lang"), 3600);
            $items = Post::PublishedSiteMap()
                ->where('lang', $lang)
                ->where(function ($query) {
                    $query->where('show_sitemap', 1)
                        ->orWhereJsonContains('json_metas->hide_section', 0);
                })
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($items as $item) {
                if ($item->type === 'faqs') {
                    continue;
                }
                if (@$item->json_metas['ctemplate'] != "disable") {
                    $sitemap->add($item->getLink(), $item->updated_at, '0.9', 'daily');
                }
            }

            $last_update = Post::PublishedSiteMap()
                ->where('show_sitemap', 1)
                ->orderBy('updated_at', 'DESC')
                ->first();
        if($last_update){
            $sitemap->add(url("/$lang"), $last_update->updated_at, '1.0', 'daily');
            $sitemap->add(url("$lang/contact-us"), null, '0.8', 'monthly');
        }
            return $sitemap->render('xml');
        }
    }
}
