<?php

namespace Progmix\PopupMessages;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class PopupMessagesSliderAction extends Action
{
    public function handle()
    {
        $this->addAction(Action::INIT_ACTION, [$this, 'registerResource']);
        $this->addAction(
            'resource.popups-messages.form_left',
            [$this, 'addFormBanner']
        );

        $this->addFilter(
            'resource.popups-messages.parseDataForSave',
            [$this, 'parseDataForSave']
        );
    }

    public function registerResource()
    {
        HookAction::registerResource(
            'popups-messages',
            null,
            [
                'label' => trans_cms('popmes::content.popup_messages'),
                'menu'  => [
                    'icon'        => 'fa fa-envelope',
                    'position'    => 20,
                    'parent'      => 'appearance',
                    'permissions' => [
                        'resource_popups-messages.index',
                    ]
                ],
                'metas' => [
                    'type' => [
                        'label' => trans_cms('popmes::content.type'),
                        'type'  => 'select',
                        'data'  => [
                            'hidden'  => true,
                            'options' => ['message' => trans_cms('popmes::content.message'), 'popup' => trans_cms('popmes::content.popup'), 'header_bar' => trans_cms('popmes::content.header_bar'),]
                        ],
                    ],
                    'in_all_pages' => [
                        'label' => trans_cms('popmes::content.in_all_pages'),
                        'type'  => 'checkbox',
                    ]

                ],
            ]
        );
    }

    public function addFormBanner($model)
    {
        $btn_directions  = ["right", "left", "center"];
        $data['banners'] = json_decode($model->getMeta('content'), true);
        $theme_colors    = get_config('theme_colors');

        $banner_items = json_decode($model->getMeta('content'), true);
        if (isset($banner_items)) {
            foreach ($banner_items as &$item) {
                $item = (object)$item;
                if (isset($item->description)) {
                    foreach ($theme_colors as $key => $color) {
                        $item->description = str_replace('color: ' . $key, 'color: ' . $color, $item->description);
                        $item->description = str_replace('background-color: ' . $key, 'background-color: ' . $color, $item->description);
                    }
                }
            }
            $data['banners'] = $banner_items;
        }

        echo e(view('popmes::slider.form', compact('model', 'btn_directions', 'data')));
    }

    public function parseDataForSave($attributes)
    {

        $titles       = $attributes['titles'] ?? [];
        $links        = $attributes['links'] ?? [];
        $images       = $attributes['images'] ?? [];
        $descriptions = $attributes['descriptions'] ?? [];
        $newTab       = $attributes['new_tabs'] ?? [];
        $date         = $attributes['s_date'] ?? [];
        $end_date     = $attributes['s_end_date'] ?? [];
        $bg_colors    = $attributes['bg_colors'] ?? [];
        $text_colors  = $attributes['text_colors'] ?? [];
        $btn_dirs     = $attributes['btn_dirs'] ?? [];
        $button_links = $attributes['button_links'] ?? [];
        $statuses     = $attributes['statuses'] ?? [];
        $theme_colors = get_config('theme_colors');

        $content = [];
        foreach ($titles as $key => $title) {
            $bg_color = null;
            if (isset($bg_colors[$key])) {
                $bg_color  = $bg_colors[$key];
                $color_key = array_search($bg_color, $theme_colors);
                if ($color_key !== false) {
                    $bg_color = $color_key;
                }
            }
            $text_color = null;
            if (isset($text_colors[$key])) {
                $text_color     = $text_colors[$key];
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

            $content[] = [
                'title'       => $title,
                'link'        => $links[$key] ?? null,
                'image'       => $images[$key] ?? null,
                'description' => $parsed_descHtml,
                'new_tab'     => $newTab[$key] ?? 0,
                'date'        => $date[$key] ?? null,
                'end_date'    => $end_date[$key] ?? null,
                'bg_color'    => $bg_color,
                'text_color'  => $text_color,
                'btn_dir'     => $btn_dirs[$key] ?? null,
                'btn_link'    => $button_links[$key] ?? null,
                'status'      => $statuses[$key] ?? null,
            ];
        }

        $attributes['meta']['content'] = $content;

        return $attributes;
    }
}
