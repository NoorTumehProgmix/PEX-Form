<?php

namespace Juzaweb\Frontend\Http\Controllers;

use Juzaweb\Backend\Models\Post;
use Illuminate\Http\Request;
use Juzaweb\Frontend\Http\Resources\PostResourceCollection;
use Juzaweb\CMS\Facades\Plugin;
use Juzaweb\Backend\Models\Plugins\SearchLog;
use Illuminate\Support\Str;

class SearchController extends Controller
{

    public function index(Request $request)
    {
        $keyword = $request->input('q');

        $title   = $keyword ? __('Search results for') . " $keyword" : __('Search Results');
        $query   = Post::published()->where(function ($query) use ($keyword) {
            $query->whereSearch(['q' => $keyword])
                ->where("lang", app()->getLocale())
                ->orderBy('display_order', 'asc')
                ->orderBy('date', 'desc');
        });

        // if (pluginActive('progmix/search-logo') && !empty($keyword)) {
        //     $content['user_agent'] = $request->header('User-Agent');

        //     SearchLog::create([
        //         'text'       => $keyword,
        //         'lang'       => app()->getLocale(),
        //         'ip_address' => $request->ip(),
        //         'data'       => json_encode($content),
        //     ]);
        // }

        $posts = $query->paginate(get_config('posts_per_page', 12));
        $posts->appends($request->query());
        $page = PostResourceCollection::make($posts)->response()->getData(true);
        return view('frontend::search', compact('page', 'posts', 'title', 'keyword'));
    }
}
