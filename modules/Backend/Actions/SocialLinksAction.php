<?php



namespace Juzaweb\Backend\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class SocialLinksAction extends Action
{
    public function handle()
    {
        $this->addAction(Action::BACKEND_INIT, [$this, 'addConfigs']);
    }

    /**
     * Add social links configurations
     *
     *
     * @return void
     */
    public function addConfigs(): void
    {
        $this->hookAction->registerConfig(
            [
                'facebook' => [
                    'form' => 'social-links',
                    'label' => "Facebook",

                ],
                'twitter' => [
                    'form' => 'social-links',
                    'label' => "X (Twitter)",

                ],
                'instagram' => [
                    'form' => 'social-links',
                    'label' => "Instagram",
                ],
                'youtube' => [
                    'form' => 'social-links',
                    'label' => "Youtube",
                ],
                'telegram' => [
                    'form' => 'social-links',
                    'label' => "Telegram",

                ],
                'linkedin' => [
                    'form' => 'social-links',
                    'label' => "Linkedin",

                ],
                'whatsapp' => [
                    'form' => 'social-links',
                    'label' => "Whatsapp",
                    'data' => [
                        'description' => 'ex: https://wa.me/970555555555',
                    ],
                ],
                'tiktok' => [
                    'form' => 'social-links',
                    'label' => "Tiktok",
                ],
                'snapchat'  => [
                    'form'  => 'social-links',
                    'label' => "Snapchat",
                ],
                'messenger' => [
                    'form'  => 'social-links',
                    'label' => "Messenger",
                ],
                'soundcloud' => [
                    'form'  => 'social-links',
                    'label' => "Soundcloud",
                ],
            ]
        );

        $this->hookAction->addSettingForm(
            'social-links',
            [
                'name' => trans_cms('cms::app.social_links'),
                'priority' => 2,
            ]
        );
    }
}
