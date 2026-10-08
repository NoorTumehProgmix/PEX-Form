<?php

namespace Juzaweb\ImageSlider;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\Backend\Jobs\ProcessImage;

class ImageSliderAction extends Action
{
    public function handle()
    {
        $this->addAction(Action::INIT_ACTION, [$this, 'registerResource']);
        $this->addAction(
            'resource.sliders.form_left',
            [$this, 'addFormBanner']
        );

        $this->addFilter(
            'resource.sliders.parseDataForSave',
            [$this, 'parseDataForSave']
        );
    }

    public function registerResource()
    {
        HookAction::registerResource(
            'sliders',
            null,
            [
                'label' => trans_cms('juim::content.sliders'),
                'menu' => [
                    'icon' => 'fa fa-sliders',
                    'position' => 6,
                    'parent' => 'appearance',
                    'permissions' => [
                        'resource_sliders.index',
                    ]
                ],
                'metas' => [
                    'content' => [
                        'type' => 'textarea',
                        'data' => [
                            'hidden' => true,
                        ]
                    ]
                ],
            ]
        );
    }

    public function addFormBanner($model)
    {
        $btn_directions = ["right", "left", "center"];
        $data['banners'] = json_decode($model->getMeta('content'), true);
        $theme_colors = get_config('theme_colors');

        $banner_items = json_decode($model->getMeta('content'), true);
        if (isset($banner_items)) {
            foreach ($banner_items as &$item) {
                $item = (object) $item;
                if (isset($item->description)) {
                    foreach ($theme_colors as $key => $color) {
                        $item->description = str_replace('color: ' . $key, 'color: ' . $color, $item->description);
                        $item->description = str_replace('background-color: ' . $key, 'background-color: ' . $color, $item->description);
                    }
                }
            }
            $data['banners'] = $banner_items;
        }

        echo e(view('juim::slider.form', compact('model', 'btn_directions', 'data')));
    }

    public function parseDataForSave($attributes)
    {
        $subtitles = $attributes['subtitles'] ?? [];
        $register_buttons = $attributes['register_buttons'] ?? [];
        $readmore_buttons = $attributes['readmore_buttons'] ?? [];
        $titles = $attributes['titles'] ?? [];
        $links = $attributes['links'] ?? [];
        $images = $attributes['images'] ?? [];
        $secondary_images = $attributes['secondary_images'] ?? [];
        $descriptions = $attributes['descriptions'] ?? [];
        $newTab = $attributes['new_tabs'] ?? [];
        $date = $attributes['s_date'] ?? [];
        $bg_img = $attributes['bg_img'] ?? [];
        $start_time = $attributes['s_start_time'] ?? [];
        $addresses = $attributes['addresses'] ?? [];
        $bg_colors = $attributes['bg_colors'] ?? [];
        $text_colors = $attributes['text_colors'] ?? [];
        $btn_dirs = $attributes['btn_dirs'] ?? [];
        $button_links = $attributes['button_links'] ?? [];
        $statuses = $attributes['statuses'] ?? [];
        $theme_colors = get_config('theme_colors');

        $content = [];
        foreach ($titles as $key => $title) {
            $bg_color = null;
            if (isset($bg_colors[$key])) {
                $bg_color = $bg_colors[$key];
                $color_key = array_search($bg_color, $theme_colors);
                if ($color_key !== false) {
                    $bg_color = $color_key;
                }
            }
            $text_color = null;
            if (isset($text_colors[$key])) {
                $text_color = $text_colors[$key];
                $text_color_key = array_search($text_color, $theme_colors);
                if ($text_color_key !== false) {
                    $text_color = $text_color_key;
                }
            }

            $parsed_desc = null;
            if (isset($descriptions[$key])) {
                $desc = $descriptions[$key];
                foreach ($theme_colors as $ckey => $color) {
                    $desc = str_replace('color: ' . $color, 'color: ' . $ckey, $desc);
                    $desc = str_replace('background-color: ' . $color, 'background-color: ' . $ckey, $desc);
                }
                $parsed_desc = $desc;
            }
            $parsed_descHtml = processEditorContent($parsed_desc);

            if (isset($images[$key])) {
                $processImageJob = new ProcessImage($images[$key], "scale", ['2500x1500'], config('juzaweb.filemanager.image-quality'), 'resized');
                dispatch($processImageJob->onQueue("sliders"));
            }

            if (isset($secondary_images[$key])) {
                $processImageJob = new ProcessImage($secondary_images[$key], "scale", ['1000x600'], config('juzaweb.filemanager.image-quality'), 'resized');
                dispatch($processImageJob->onQueue("sliders"));
            }

            $content[] = [
                'subtitle' => $subtitles[$key] ?? null,
                'title' => $title,
                'register_button' => $register_buttons[$key] ?? null,
                'readmore_button' => $readmore_buttons[$key] ?? null,
                'link' => $links[$key] ?? null,
                'image' => $images[$key] ?? null,
                'secondary_image' => $secondary_images[$key] ?? null,
                'description' => $parsed_descHtml,
                'new_tab' => $newTab[$key] ?? 0,
                'date' => $date[$key] ?? null,
                'bg_img' => $bg_img[$key] ?? null,
                'start_time' => $start_time[$key] ?? null,
                'address' => $addresses[$key] ?? null,
                'bg_color' => $bg_color,
                'text_color' => $text_color,
                'btn_dir' => $btn_dirs[$key] ?? null,
                'btn_link' => $button_links[$key] ?? null,
                'status'   => $statuses[$key] ?? null,
            ];
        }

        $attributes['meta']['content'] = $content;

        return $attributes;
    }
}
