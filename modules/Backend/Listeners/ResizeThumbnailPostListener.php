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

class ResizeThumbnailPostListener
{
    public function handle(AfterPostSave $event, $job = "posts1", $folder = "resized"): void
    {
        //Resize thumb
        if (!empty($event->post->thumbnail) && !is_url($event->post->thumbnail)) {
            $resize = get_config('auto_resize_thumbnail')[$event->post->type] ?? false;
            $sizes = get_thumbnail_size($event->post->type);
            $quality = get_config('thumbnail_quality')[$event->post->type] ?? config('juzaweb.filemanager.image-quality');

            if (!empty($resize) && !empty($sizes)) {
                $processImageJob = new ProcessImage($event->post->thumbnail, "scale", $sizes, $quality, $folder);
                dispatch($processImageJob->onQueue($job));
            }
        }

        $secondary_image = $event->post->json_metas['secondary_image'] ?? null;
        if (isset($secondary_image) && !empty($secondary_image) && !is_url($secondary_image)) {
            $resize = get_config('auto_resize_thumbnail')[$event->post->type] ?? false;
            $sizes = get_thumbnail_size($event->post->type);
            $quality = get_config('thumbnail_quality')[$event->post->type] ?? config('juzaweb.filemanager.image-quality');

            if (!empty($resize) && !empty($sizes)) {
                $processImageJob = new ProcessImage($secondary_image, "scale", $sizes, $quality, $folder);
                dispatch($processImageJob->onQueue($job));
            }
        }

        //Resize background image
        $background_image = $event->post->json_metas['background_image'] ?? null;
        if (isset($background_image) && !empty($background_image) && !is_url($background_image)) {
            $processImageJob = new ProcessImage($background_image, "scale", ["2500x1500"], config('juzaweb.filemanager.image-quality'), $folder, postType: $event->post->type);
            dispatch($processImageJob->onQueue($job));
        }

        //Resize facebook og Image
        $og_image = $event->post->json_metas['metas']['meta_og_image'];
        if (!empty($og_image) && !is_url($og_image)) {
            //not webp
            $processImageJob = new ProcessImage($og_image, "cover", ["1200x630"], 100, $folder);
            dispatch($processImageJob->onQueue($job));
        }

        //Resize twitter Image
        $twitter_image = $event->post->json_metas['metas']['meta_twitter_image'];
        if (!empty($twitter_image) && !is_url($twitter_image)) {
            //not webp
            $processImageJob = new ProcessImage($twitter_image, "cover", ["1200x675"], 100, $folder);
            dispatch($processImageJob->onQueue($job));
        }
    }
}
