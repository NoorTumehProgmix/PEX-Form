<?php



namespace Juzaweb\Backend\Actions;

use Juzaweb\Backend\Http\Controllers\Backend\Setting\SeoController;
use Juzaweb\Backend\Models\SeoMeta;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\CMS\Models\Model;

class SeoAction extends Action
{
    public function handle()
    {
        $this->addAction(
            Action::POSTS_FORM_LEFT_ACTION,
            [$this, 'addFormSeo'],
            999
        );
        $this->addAction(
            Action::POSTS_FORM_SEO_FORM_CREATE,
            [$this, 'addFormSeoCreate'],
            999
        );

        $this->addAction(
            Action::BACKEND_INIT,
            [$this, 'addAjaxs']
        );

        $this->addAction(Action::BACKEND_INIT, [$this, 'addMenu']);
    }

    public function addMenu()
    {
        $this->hookAction->registerConfig(
            [
                'jw_enable_sitemap' => [
                    'form' => 'seo',
                    'type' => 'checkbox',
                    'label' => trans_cms('cms::app.seo.enable_sitemap'),
                    'data' => [
                        'default' => 1,
                        'description' => trans_cms('cms::app.seo.enable_sitemap_description'),
                    ]
                ],
                'jw_enable_post_feed' => [
                    'type' => 'checkbox',
                    'label' => trans_cms('cms::app.seo.enable_post_feed'),
                    'form' => 'seo',
                    'data' => [
                        'default' => 1
                    ]
                ],
                'jw_enable_taxonomy_feed' => [
                    'type' => 'checkbox',
                    'label' => trans_cms('cms::app.seo.enable_taxonomy_feed'),
                    'form' => 'seo',
                    'data' => [
                        'default' => 1
                    ]
                ],
                'jw_auto_ping' => [
                    'type' => 'checkbox',
                    'label' => trans_cms('cms::app.seo.enable_auto_ping'),
                    'form' => 'seo',
                    'data' => [
                        'description' => trans_cms('cms::app.seo.auto_ping_description')
                    ]
                ],
                'jw_bing_api_key' => [
                    'label' => trans_cms('cms::app.seo.bing_api_key'),
                    'form' => 'seo',
                ],
                'bing_verify_key' => [
                    'label' => trans_cms('cms::app.seo.bing_verify_key'),
                    'form' => 'seo',
                ],
                'google_verify_key' => [
                    'label' => trans_cms('cms::app.seo.google_verify_key'),
                    'form' => 'seo',
                ]
            ]
        );

        HookAction::addSettingForm(
            'seo',
            [
                'name' => trans_cms('cms::app.seo_setting'),
                'priority' => 20,
            ]
        );
    }

    public function addAjaxs()
    {
        HookAction::registerAdminAjax(
            'seo-content',
            [
                'callback' => [SeoController::class, 'getStringRaw'],
                'method' => 'post'
            ]
        );
    }

    public function addFormSeo(Model $model)
    {
        $data = SeoMeta::findByModel($model);

        echo e(
            view(
                'cms::backend.items.seo_form',
                compact(
                    'model',
                    'data'
                )
            )
        );
    }
    public function addFormSeoCreate($data)
    {
        $model = $data->model;
        echo e(
            view(
                'cms::backend.items.seo_form',
                compact(
                    'model',
                    'data'
                )
            )
        );
    }
}
