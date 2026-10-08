<?php

namespace Juzaweb\Backend\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Juzaweb\Backend\Listeners\ResizeThumbnailPostListener;
use Juzaweb\Backend\Listeners\ResizeGalleryPostListener;
use Juzaweb\Backend\Events\AfterPostSave;
use Juzaweb\Backend\Models\Post;
use Illuminate\Support\Collection;

class ResizePostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $posts;
    protected $job_name;
    protected $folder;

    public function __construct(Collection $posts, string $job_name,string $folder)
    {
        $this->posts = $posts;
        $this->job_name = $job_name;
        $this->folder = $folder;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $thumb_listener = new ResizeThumbnailPostListener();
        $gallery_listener = new ResizeGalleryPostListener();

        foreach ($this->posts as $post) {
            $event = new AfterPostSave($post, []);
            $thumb_listener->handle($event, $this->job_name,$this->folder);
            $gallery_listener->handle($event, $this->job_name,$this->folder);
        }
    }
}
