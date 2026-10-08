<?php

namespace Juzaweb\Frontend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Juzaweb\Frontend\Events\PostViewed;
use Juzaweb\Frontend\Http\Resources\PostResourceCollection;
use Juzaweb\Backend\Models\Post;

use Juzaweb\Backend\Models\Taxonomy;
use Progmix\Redirections\Models\Redirection;
use Illuminate\Support\Facades\Log;


class PostController extends Controller
{
    protected $cacheTime;

    public function __construct()
    {
        $this->cacheTime = get_config('cache_duration', 0);
    }

    public function home()
    {
        $main_post = get_page_by_template('homepage', ['pages']);
        $main_slider = generate_slider($main_post->getMeta('slider'), $main_post->id);
        $homepage_blocks = get_pages_of_type(['pages'], null, null, null, $main_post->id, 'Published');
        $seo_metas = $main_post->getMeta('metas');
        return view('frontend::home', compact('homepage_blocks', 'main_post', 'seo_metas', 'main_slider'));
    }


    public function post(Request $request, $slug = null)
    {
        $lang = app()->getLocale();
        //Check for redirection rule
        if (pluginActive("progmix/redirections")) {
            $redirection = Cache::remember("redirection-$lang-$slug", $this->cacheTime, function () use ($lang, $slug) {
                return Redirection::where('old_link', "/$lang/$slug")->first();
            });
            if ($redirection) {
                return redirect($redirection->new_link, 301);
            }
        }

        $main_post = get_page_by_slug($slug, ['pages', 'posts']);

        if ($main_post->external_link) {
            return redirect($main_post->external_link, 301);
        }
        if (in_array($main_post->status, ["draft", "preview"])) {
            if (!Auth::user()) {
                abort(404);
            }
        }

        if (isset($main_post->json_metas['hide_section']) && $main_post->json_metas['hide_section'] == 1) {
            abort(404);
        }

        event(new PostViewed($main_post));
        if ($main_post->type == "posts") {
            return $this->showPost($main_post, $request);
        }
        return $this->showPage($main_post, $request);
    }


    public function showPage($main_post, Request $request)
    {

        $template      = $main_post->getMeta('ctemplate') ?? '';
        $page_type     = $main_post->type;
        $seo_metas         = $main_post->getMeta('metas') ?? [];
        $statusLabel   = in_array($main_post->status, ['draft', 'preview']) ? 'PublishedOrDraft' : 'Published';

        // Slider
        $main_slider = generate_slider($main_post->getMeta('slider'), $main_post->id);

        // Breadcrumbs
        $breadcrumbs = breadcrumbs($main_post->path);

        $externalTypes = [
            // 'workshops' => [
            //     'name' => 'workshops',
            //     'haveNoParent' => true,
            // ],
        ];

        $postsType = 'posts';
        $haveNoParent = false;

        //Pagination and query parameters
        if (array_key_exists($template, $externalTypes)) {
            // if ($template === 'see_and_do') {
            //     request()->merge(request()->except(['category']));
            // }
            $postsType = $externalTypes[$template]['name'];
            $haveNoParent = $externalTypes[$template]['haveNoParent'];
        }

        $posts = get_posts_of_type($postsType, null, null, $haveNoParent, 'Published', $request);
        // Generate pagination data
        $pagination_page = PostResourceCollection::make($posts)->response()->getData(true);
        if ($posts->isEmpty()) {
            $pagination_page = null;
        }

        $sub_pages = get_pages_of_type('pages', null, null, false, $main_post->id, $statusLabel);
        $pagination_sub_pages = PostResourceCollection::make($sub_pages)->response()->getData(true);
        if ($sub_pages->isEmpty()) {
            $pagination_sub_pages = null;
        }

        return view('frontend::pages', compact(
            'posts',
            'sub_pages',
            'main_post',
            'main_slider',
            'pagination_page',
            'pagination_sub_pages',
            'seo_metas',
            'breadcrumbs',
            'template',
        ));
    }

    public function showPost($main_post, Request $request)
    {
        $template      = $main_post->getMeta('ctemplate') ?? '';
        $page_type     = $main_post->type;
        $seo_metas         = $main_post->getMeta('metas') ?? [];
        $statusLabel   = in_array($main_post->status, ['draft', 'preview']) ? 'PublishedOrDraft' : 'Published';

        // Slider
        $main_slider = generate_slider($main_post->getMeta('slider'), $main_post->id);

        // Breadcrumbs
        $breadcrumbs = breadcrumbs($main_post->path);
        return view("frontend::posts", compact('main_post', 'main_slider', 'seo_metas', 'breadcrumbs', 'template'));
    }
}
