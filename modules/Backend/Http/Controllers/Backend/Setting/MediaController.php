<?php

/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/juzacms
 * @author     Juzaweb Team <admin@juzaweb.com>
 * @link       https://juzaweb.com
 * @license    MIT
 */

namespace Juzaweb\Backend\Http\Controllers\Backend\Setting;

use Illuminate\Contracts\View\View;
use Juzaweb\CMS\Contracts\HookActionContract as HookAction;
use Juzaweb\CMS\Http\Controllers\BackendController;
use Illuminate\Http\Request;
use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Jobs\ResizePostsJob;
use Juzaweb\Backend\Jobs\ResizeSlidersJob;

class MediaController extends BackendController
{
    public function __construct(protected HookAction $hookAction)
    {
    }

    public function index(): View
    {
        global $jw_user;
        if (!$jw_user->can('settings.options-media')) {
            abort(403);
        }

        $title = trans_cms('cms::app.media_setting.title');
        $postTypes = $this->hookAction->getPostTypes();
        $thumbnailDefaults = get_config('thumbnail_defaults', []);
        $thumbnailSizes = $this->hookAction->getThumbnailSizes()->toArray();

        return view(
            'cms::backend.setting.media',
            compact(
                'title',
                'postTypes',
                'thumbnailDefaults',
                'thumbnailSizes'
            )
        );
    }

    public function fetchPosts(Request $request)
    {
        $posts = Post::whereNotNull('thumbnail')
            ->orWhere('images', '!=', '[]')
            ->limit(100)->get();

        $totalPosts = $posts->count();
        $halfway = (int) ceil($totalPosts / 2);

        $posts1Count = $halfway;
        $posts2Count = $totalPosts - $halfway;

        $posts1 = $posts->take($halfway);
        $posts2 = $posts->slice($halfway);

        $request->session()->flash('posts1Count', $posts1Count);
        $request->session()->flash('posts2Count', $posts2Count);

        $request->session()->put('posts1', $posts1);
        $request->session()->put('posts2', $posts2);

        return redirect()->route('admin.setting.media');
    }

    public function dispatchJobs(Request $request)
    {

        $folder = 'resized_' . date('Y-m-d_H-i-s', time());

        // Dispatch the ResizePostsJob
        ResizePostsJob::dispatch(session('posts1'), 'posts1', $folder)->onQueue('posts1');
        ResizePostsJob::dispatch(session('posts2'), 'posts2', $folder)->onQueue('posts2');

        // Dispatch the ResizeSlidersJob
        ResizeSlidersJob::dispatch($folder)->onQueue('sliders');

        return $this->success(
            [
                'message' => "Thumbnails and sliders are being processed in the background. Output folder: $folder",
            ]
        );
    }
}
