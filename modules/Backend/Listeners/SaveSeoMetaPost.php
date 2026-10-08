<?php

namespace Juzaweb\Backend\Listeners;

use Illuminate\Support\Arr;
use Juzaweb\Backend\Events\AfterPostSave;
use Juzaweb\Backend\Models\SeoMeta;
use Juzaweb\Backend\Models\Post;

class SaveSeoMetaPost
{
    public function handle(AfterPostSave $event): void
    {
      
        $data = $event->data;
        $title = Arr::get($data, 'meta_title');
        $description = Arr::get($data, 'meta_description');
        
        $keywords = Arr::get($data, 'meta_keywords');
        $keywords = $keywords != "" ? implode(',', $keywords) : "";

        if (empty($title) && empty($description)) {
            return;
        }
      
        Post::updateOrCreate(
            [
                'id' => $event->post->id,
            ],
            [
                'meta_title' => $title,
                'meta_description' => $description,
                'meta_keywords' => $keywords,
            ]
        );

        // SeoMeta::updateOrCreate(
        //     [
        //         'object_type' => 'posts',
        //         'object_id' => $event->post->id,
        //     ],
        //     [
        //         'meta_title' => $title,
        //         'meta_description' => $description,
        //         'meta_keywords' => $keywords,
        //     ]
        // );
    }
}
