<?php

/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/juzacms
 * @author     Juzaweb Team <admin@juzaweb.com>
 * @link       https://juzaweb.com
 * @license    MIT
 */

namespace Juzaweb\Backend\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Juzaweb\Backend\Events\AfterPostSave;
use Juzaweb\Backend\Jobs\ProcessImage;

class ResizeGalleryPostListener
{
    public function handle(AfterPostSave $event, $job = null, $folder = "resized"): void
    {
        //Resize images
        if (empty($event->post->images)) {
            return;
        }
        $resize = get_config('auto_resize_thumbnail')["gallery"] ?? false;
        $sizes = get_thumbnail_size("gallery");
        $quality = get_config('thumbnail_quality')["gallery"] ?? config('juzaweb.filemanager.image-quality');

        if (empty($resize) || empty($sizes)) {
            return;
        }

        foreach ($event->post->images as $index => $image) {
            if (isset($job)) {
                $queueName = $job;
            } else {
                $queueName = $index % 2 === 0 ? 'posts1' : 'posts2';
            }
            // Instantiate a new instance of ProcessImage job
            $processImageJob = new ProcessImage($image, "resize", $sizes, $quality, $folder);

            // Dispatch the job to the specific queue
            dispatch($processImageJob->onQueue($queueName));
        }
    }
}
