<?php

/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/juzacms
 * @author     Juzaweb Team <admin@juzaweb.com>
 * @link       https://juzaweb.com
 * @license    MIT
 */

namespace Juzaweb\Backend\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\Backend\Models\Post;

class MediaAction extends Action
{
    public function handle()
    {
        $this->addAction(Action::INIT_ACTION, [$this, 'addMediaConfigs']);
        $this->addAction(Action::BACKEND_INIT, [$this, 'addAdminMenu']);
    }

    public function addAdminMenu()
    {
        //Working Hours
        $working_hours_page =   Post::published()
            ->where("json_metas->ctemplate", "contact_hours")
            ->where("lang", app()->getLocale())->first();
        if ($working_hours_page) {
            $this->hookAction->addAdminMenu(
                trans_cms('cms::app.working_hours'),
                "post-type/pages/$working_hours_page->id/edit",
                [
                    'icon'        => 'fa fa-clock-o',
                    'position'    => 20,
                    'permissions' => [
                        "edit.$working_hours_page->id",
                    ],
                ]
            );
        }

        $this->hookAction->registerAdminPage(
            'options-media',
            [
                'title' => trans_cms('cms::app.media'),
                'menu' => [
                    'icon' => 'fa fa-list',
                    'position' => 30,
                    'parent' => 'setting',
                    'permissions' => [
                        'settings.options-media',
                    ],
                ]
            ]
        );
    }

    public function addMediaConfigs()
    {
        $this->hookAction->registerConfig(
            [
                'thumbnail_defaults',
            ]
        );
    }
}
