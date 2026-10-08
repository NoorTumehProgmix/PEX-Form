<?php


namespace Juzaweb\Backend\Actions;

use Illuminate\Support\Facades\Cache;
use Juzaweb\Backend\Models\Post;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\CMS\Facades\ThemeLoader;
use Juzaweb\CMS\Models\User;
use Juzaweb\CMS\Support\Notification;
use Juzaweb\CMS\Support\Theme\CustomMenuBox;
use Juzaweb\CMS\Support\Theme\GeneralLinksMenuBox;
use Juzaweb\CMS\Support\Updater\CmsUpdater;
use Juzaweb\CMS\Version;
use Juzaweb\Frontend\Http\Controllers\PageController;
use Juzaweb\Frontend\Http\Controllers\PostController;
use Illuminate\Support\Facades\Schema;
use Juzaweb\CMS\Facades\Plugin;

class MenuAction extends Action
{
    public function handle()
    {
        $this->addAction(self::INIT_ACTION, [$this, 'addDatatableSearchFieldTypes']);
        $this->addAction(self::INIT_ACTION, [$this, 'addPostTypes']);
        $this->addAction(self::BACKEND_CALL_ACTION, [$this, 'addBackendMenu']);
        $this->addAction(self::BACKEND_CALL_ACTION, [$this, 'addSettingPage']);
        $this->addAction(self::BACKEND_INIT, [$this, 'addAdminScripts'], 10);
        $this->addAction(self::BACKEND_INIT, [$this, 'addAdminStyles'], 10);
        $this->addAction(self::INIT_ACTION, [$this, 'addMenuBoxs'], 50);
        $this->addAction(self::BACKEND_CALL_ACTION, [$this, 'addTaxonomiesForm']);
        $this->addAction(self::INIT_ACTION, [$this, 'registerEmailHooks']);
        $this->addAction(Action::BACKEND_INIT, [$this, 'addConfigs']);
    }


    public function addBackendMenu()
    {
        HookAction::addAdminMenu(
            trans_cms('cms::app.dashboard'),
            'dashboard',
            [
                'icon' => 'fa fa-dashboard',
                'position' => 1,
            ]
        );

        if (config('juzaweb.plugin.enable_upload')) {
            HookAction::addAdminMenu(
                trans_cms('cms::app.dashboard'),
                'dashboard',
                [
                    'icon' => 'fa fa-dashboard',
                    'position' => 1,
                    'parent' => 'dashboard',
                ]
            );
        }

        HookAction::addAdminMenu(
            trans_cms('cms::app.media'),
            'media',
            [
                'icon' => 'fa fa-photo',
                'position' => 2,
                'permissions' => [
                    'media.index',

                ],
            ]
        );

        HookAction::addAdminMenu(
            trans_cms('cms::app.appearance'),
            'appearance',
            [
                'icon' => 'fa fa-paint-brush',
                'position' => 20,
                'permissions' => [
                    'menus.index',

                ],
            ]
        );

        HookAction::addAdminMenu(
            trans_cms('cms::app.menus'),
            'menus',
            [
                'icon' => 'fa fa-list',
                'position' => 2,
                'parent' => 'appearance',
                'permissions' => [
                    'menus.index',
                ],
            ]
        );

        HookAction::addAdminMenu(
            trans_cms('cms::app.permalinks'),
            'permalinks',
            [
                'icon' => 'fa fa-link',
                'position' => 15,
                'parent' => 'setting',
                'permissions' => [
                    'settings.permalinks',
                ],
            ]
        );

        HookAction::addAdminMenu(
            trans_cms('cms::app.plugins'),
            'plugins',
            [
                'icon' => 'fa fa-plug',
                'position' => 50,
            ]
        );

        if (config('juzaweb.plugin.enable_upload')) {
            HookAction::addAdminMenu(
                trans_cms('cms::app.plugins'),
                'plugins',
                [
                    'icon' => 'fa fa-plug',
                    'position' => 1,
                    'parent' => 'plugins',
                    'permissions' => [
                        'plugins.index',
                        'plugins.edit',
                        'plugins.create',
                        'plugins.delete',
                    ],
                ]
            );

            HookAction::addAdminMenu(
                trans_cms('cms::app.add_new'),
                'plugin.install',
                [
                    'icon' => 'fa fa-plus',
                    'position' => 1,
                    'parent' => 'plugins',
                    'permissions' => [
                        'plugins.create',
                    ],
                ]
            );
        }

        HookAction::addAdminMenu(
            trans_cms('cms::app.setting'),
            'setting',
            [
                'icon' => 'fa fa-cogs',
                'position' => 70,
            ]
        );

        HookAction::addAdminMenu(
            trans_cms('cms::app.managements'),
            'managements',
            [
                'icon' => 'fa fa-cogs',
                'position' => 75,
            ]
        );

        HookAction::addAdminMenu(
            trans_cms('cms::app.general_setting'),
            'setting.system',
            [
                'icon' => 'fa fa-cogs',
                'position' => 1,
                'parent' => 'setting',
                'permissions' => [
                    'settings.general',
                ],
            ]
        );

        HookAction::addAdminMenu(
            trans_cms('cms::app.users'),
            'users',
            [
                'icon' => 'fa fa-user-circle-o',
                'position' => 40,
                'parent' => 'managements',
                'permissions' => [
                    'users.index',
                    'users.edit',
                    'users.create',
                    'users.delete',
                ],
            ]
        );

        HookAction::addAdminMenu(
            trans_cms('cms::app.email_templates'),
            'email-template',
            [
                'icon' => 'fa fa-envelope',
                'position' => 50,
                'parent' => 'managements',
                'permissions' => [
                    'email_templates.index',
                    'email_templates.edit',
                    'email_templates.create',
                    'email_templates.delete',
                ],
            ]
        );

        if (!config('network.enable')) {
            HookAction::addAdminMenu(
                trans_cms('cms::app.email_logs'),
                'logs.email',
                [
                    'icon' => 'fa fa-cogs',
                    'position' => 51,
                    'parent' => 'managements',
                    'permissions' => [
                        'email_logs.index',
                    ],

                ]
            );
        }
        HookAction::addAdminMenu(
            trans_cms('cms::app.actions_logs'),
            'logs.actions',
            [
                'icon' => 'fa fa-cogs',
                'position' => 52,
                'parent' => 'managements',
                'permissions' => [
                    'actions_logs.index',
                ],
            ]
        );
    }

    public function addSettingPage()
    {
        HookAction::addSettingForm(
            'general',
            [
                'name' => trans_cms('cms::app.general_setting'),
                'view' => 'cms::backend.setting.system.form.general',
                'priority' => 1,
            ]
        );

        HookAction::addSettingForm(
            'email',
            [
                'name' => trans_cms('cms::app.email_setting'),
                'view' => 'cms::backend.email.setting',
                'header' => false,
                'footer' => false,
                'priority' => 50,
            ]
        );
        HookAction::addSettingForm(
            'prefix',
            [
                'name' => trans_cms('cms::app.generate_prefix'),
                'view' => 'cms::backend.setting.system.form.prefix',
                'header' => false,
                'footer' => false,
                'priority' => 50,
            ]
        );
        HookAction::addSettingForm(
            'hosts',
            [
                'name' => trans_cms('cms::app.hosts'),
                'view' => 'cms::backend.setting.system.form.hosts',
                'header' => false,
                'footer' => false,
                'priority' => 50,
            ]
        );
        HookAction::addSettingForm(
            'templates',
            [
                'name' => trans_cms('cms::app.templates_management'),
                'view' => 'cms::backend.setting.system.form.templates',
                'header' => false,
                'footer' => false,
                'priority' => 50,
            ]
        );
        HookAction::addSettingForm(
            'robots',
            [
                'name' => trans_cms('cms::app.robots'),
                'view' => 'cms::backend.setting.system.form.robots',
                'header' => false,
                'footer' => false,
                'priority' => 50,
            ]
        );
    }

    public function addPostTypes()
    {
        $templates = ThemeLoader::getTemplates("default");
        $landingPagesTemplates = ThemeLoader::getRegister("default", 'landing_pages');

        $data = [
            'options' => ['' => trans_cms('cms::app.choose_template')],
        ];
        $landingPagesData = [
            'options' => ['' => trans_cms('cms::app.choose_template')],
        ];
        if (isset($templates)) {
            foreach ($templates as $key => $template) {
                $data['options'][$key] = [
                    'label' => $template['label'],
                    'data' => [
                        'has-block' => ($template['blocks'] ?? 0) ? 1 : 0,
                    ],
                ];
            }
        }

        if (isset($landingPagesTemplates)) {
            foreach ($landingPagesTemplates as $key => $template) {
                $landingPagesData['options'][$key] = [
                    'label' => $template['label'],
                    'data' => [
                        'has-block' => ($template['blocks'] ?? 0) ? 1 : 0,
                    ],
                ];
            }
        }

        HookAction::registerPostType(
            'pages',
            [
                'label' => trans_cms('cms::app.pages'),
                'model' => Post::class,
                'menu_icon' => 'fa fa-edit',
                'rewrite' => false,
                'menu_position' => 15,
                'custom_fields' => [
                    'subtitle',
                    'editor',
                    'thumbnail',
                    'repeater',
                    'show_sitemap',
                    'hide_section',
                    'inner_page_design',
                    'seo',
                    'images',
                    'button_text',
                    'button_url',
                ],
                'metas' => [
                    'secondary_image' => [
                        'type' => 'image',
                        'sidebar' => false,
                    ],
                    'ctemplate' => [
                        'type' => 'modal_preview',
                        'label' => trans_cms('cms::app.template'),
                        'sidebar' => true,
                        'data' => $data,
                    ],
                    'parent' => [
                        'type' => 'post',
                        'label' => trans_cms('cms::app.parent'),
                        'name' => "parent",
                        'data' => [
                            'type' => "pages",
                        ],
                    ],
                    // 'defaults'         => [
                    //     'type'  => 'post',
                    //     'label' => trans_cms('cms::app.default_pages'),
                    //     'name'  => "pages",
                    //     'data'  => [
                    //         'multiple' => true,
                    //         'type'     => "pages",
                    //     ],
                    // ],
                    'slider' => [
                        'type' => 'resource',
                        'label' => trans_cms('cms::app.insert_slider'),
                        'name' => "sliders",
                        'sidebar' => true,
                        'data' => [
                            'multiple' => false,
                            'type' => "sliders",
                        ],
                    ],
                    // 'faq_id'           => [
                    //     'type'    => 'taxonomy_parent',
                    //     'label'   => trans_cms('cms::app.insert_faqs'),
                    //     'name'    => "faq",
                    //     'sidebar' => true,
                    //     'data'    => [
                    //         'multiple'  => false,
                    //         'post_type' => "faqs",
                    //         'parents'   => 'categories',
                    //         'taxonomy'  => 'categories',
                    //     ],
                    // ],
                    'block_content' => [
                        'visible' => false,
                        'sidebar' => true,
                    ],
                ],
            ]
        );

        HookAction::registerPostType(
            'speakers',
            [
                'label' => 'المتحدثون',
                'model' => Post::class,
                'menu_icon' => 'fa fa-users',
                'menu_position' => 17,
                'callback' => PostController::class,
                'custom_fields' => [
                    'subtitle',   // الدور الوظيفي → $post->subtitle
                    'editor',     // نص الوصف الأطول (يظهر في نافذة التعريف) → $post->content
                    'thumbnail',  // صورة المتحدث → $post->thumbnail
                ],
                'metas' => [
                    'role_type' => [
                        'type' => 'select',
                        'label' => 'نوع الدور',
                        'sidebar' => true,
                        'data' => [
                            'options' => [
                                'speaker' => 'متحدث',
                                'moderator' => 'مدير جلسة',
                            ],
                        ],
                    ],
                    'bio' => [
                        'type' => 'text',
                        'label' => 'نبذة قصيرة (تظهر في البطاقة)',
                        'sidebar' => false,
                    ],
                ],
            ]
        );

        HookAction::registerPostType(
            'agenda',
            [
                'label' => 'البرنامج',
                'model' => Post::class,
                'menu_icon' => 'fa fa-calendar',
                'menu_position' => 18,
                'callback' => PostController::class,
                'custom_fields' => [
                    'editor', // الوصف الطويل (يظهر عند فتح "تفاصيل الجلسة") → $post->content
                ],
                'metas' => [
                    'agenda_date' => [
                        'type' => 'text',
                        'label' => 'تاريخ البرنامج',
                        'sidebar' => true,
                    ],
                    'start_time' => [
                        'type' => 'text',
                        'label' => 'وقت البداية (مثال: 10:05)',
                        'sidebar' => true,
                    ],
                    'end_time' => [
                        'type' => 'text',
                        'label' => 'وقت النهاية (مثال: 10:10)',
                        'sidebar' => true,
                    ],
                    'session_type' => [
                        'type' => 'text',
                        'label' => 'نوع العنصر (النص الظاهر، مثال: جلسة افتتاحية)',
                        'sidebar' => true,
                    ],
                    'type_key' => [
                        'type' => 'select',
                        'label' => 'تصنيف العنصر (يتحكم بالتنسيق/CSS)',
                        'sidebar' => true,
                        'data' => [
                            'options' => [
                                'plain' => 'عادي (plain)',
                                'session' => 'جلسة (session)',
                            ],
                        ],
                    ],
                    'has_details' => [
                        'type' => 'checkbox',
                        'label' => 'له تفاصيل إضافية (زر "تفاصيل الجلسة")',
                        'sidebar' => true,
                    ],
                    'chair' => [
                        'type' => 'post',
                        'label' => 'مدير الجلسة',
                        'name' => 'chair',
                        'sidebar' => true,
                        'data' => [
                            'type' => 'speakers',
                        ],
                    ],
                    'session_speakers' => [
                        'type' => 'post',
                        'label' => 'المتحدثون في هذه الجلسة',
                        'name' => 'session_speakers',
                        'sidebar' => true,
                        'data' => [
                            'multiple' => true,
                            'type' => 'speakers',
                        ],
                    ],
                ],
            ]
        );

        HookAction::registerPostType(
            'sponsors',
            [
                'label' => 'الرعاة',
                'model' => Post::class,
                'menu_icon' => 'fa fa-money',
                'menu_position' => 17,
                'callback' => PostController::class,
                'custom_fields' => [
                    'thumbnail',
                    'editor',
                ],
                'metas' => [
                    'sponsor_type' => [
                        'type' => 'select',
                        'label' => 'نوع الرعاة',
                        'sidebar' => true,
                        'data' => [
                            'options' => [
                                'diamond' => 'الرعاة الماسيون',
                                'gold' => 'الرعاة الذهبيون',
                                'silver' => 'الرعاة الفضيون',
                            ],
                        ],
                    ],
                ],
            ]
        );

        HookAction::registerPostType(
            'posts',
            [
                'label' => trans_cms('cms::app.posts'),
                'model' => Post::class,
                'menu_icon' => 'fa fa-edit',
                'menu_position' => 16,
                'callback' => PostController::class,
                'custom_fields' => [
                    'subtitle',
                    'editor',
                    'text',
                    'thumbnail',
                    'images',
                    'pin',
                    'show_sitemap',
                    'hide_section',
                    'seo'
                ],
                'metas' => [
                    'video' => [
                        'type' => 'file',
                        'sidebar' => true,
                        'data' => [
                            'multiple' => false,
                            'type' => "video",
                        ],
                    ],
                    'parent' => [
                        'type' => 'post',
                        'label' => trans_cms('cms::app.parent'),
                        'name' => "pages",
                        'data' => [
                            'type' => "pages",
                        ],
                    ]
                ],
                // 'supports'      => [
                //     'tag',
                // ],
            ]
        );
        HookAction::registerPostType(
            'test',
            [
                'label' => trans_cms('cms::app.test'),
                'model' => Post::class,
                'menu_icon' => 'fa fa-calender',
                'menu_position' => 16,
                'callback' => PostController::class,
                'custom_fields' => [
                    'subtitle',
                    'editor',
                    'text',
                    'images',
                    'show_sitemap',
                    'hide_section',
                    'seo'
                ],

                'parent' => [
                    'type' => 'post',
                    'label' => trans_cms('cms::app.parent'),
                    'name' => "pages",
                    'data' => [
                        'type' => "pages",
                    ],
                ]
            ],
            // 'supports'      => [
            //     'tag',
            // ],

        );

        // HookAction::registerPostType(
        //     'landing_pages',
        //     [
        //         'label'         => trans_cms('cms::app.landing_pages'),
        //         'model'         => Post::class,
        //         'menu_icon'     => 'fa fa-edit',
        //         'callback'      => PostController::class,
        //         'menu_position' => 17,
        //         'metas'         => [
        //             'ctemplate'        => [
        //                 'type'          => 'select',
        //                 'label'         => trans_cms('cms::app.template'),
        //                 'sidebar'       => true,
        //                 'data'          => $landingPagesData,
        //                 'show_in_root'  => false,
        //                 'show_in_child' => true,
        //             ],
        //             'parent'           => [
        //                 'type'          => 'post',
        //                 'label'         => trans_cms('cms::app.parent'),
        //                 'name'          => "pages",
        //                 'sidebar'       => true,
        //                 'data'          => [
        //                     'type' => "landing_pages",
        //                 ],
        //                 'show_in_root'  => false,
        //                 'show_in_child' => true,
        //             ],
        //             'youtube_url'      => [
        //                 'type'            => 'text',
        //                 'show_in_root'    => false,
        //                 'show_in_child'   => true,
        //                 'show_if_visible' => true,
        //             ],
        //             'is_slider'        => [
        //                 'type'            => 'checkbox',
        //                 'show_in_root'    => false,
        //                 'show_in_child'   => true,
        //                 'show_if_visible' => true,
        //             ],
        //             'hide_title'       => [
        //                 'type'          => 'checkbox',
        //                 'show_in_root'  => false,
        //                 'show_in_child' => true,
        //             ],
        //             'hide_from_menu'   => [
        //                 'type'          => 'checkbox',
        //                 'show_in_root'  => false,
        //                 'show_in_child' => true,
        //             ],
        //             'background_color' => [
        //                 'type'          => 'text',
        //                 'data'          => [
        //                     'type' => "color",
        //                 ],
        //                 'show_in_root'  => false,
        //                 'show_in_child' => true,
        //             ],
        //             'text_color'       => [
        //                 'type'          => 'text',
        //                 'data'          => [
        //                     'type' => "color",
        //                 ],
        //                 'show_in_root'  => false,
        //                 'show_in_child' => true,
        //             ],
        //             'background_image' => [
        //                 'type'          => 'image',
        //                 'show_in_root'  => false,
        //                 'show_in_child' => true,
        //             ],
        //         ],
        //     ]
        // );
    }

    public function addMenuBoxs()
    {
        HookAction::registerMenuBox(
            'custom_url',
            [
                'title' => trans_cms('cms::app.custom_url'),
                'group' => 'custom',
                'menu_box' => new CustomMenuBox(),
            ]
        );
        $plugins = Plugin::all();
        if (isset($plugins['progmix/links']) and $plugins['progmix/links']->isEnabled()) {
            HookAction::registerMenuBox(
                'general_url',
                [
                    'title' => trans_cms('links::content.name'),
                    'group' => 'general_links',
                    'menu_box' => new GeneralLinksMenuBox(),
                ]
            );
        }
    }

    public function addTaxonomiesForm()
    {


        $types = HookAction::getPostTypes();
        foreach ($types as $key => $type) {
            add_action(
                "post_type.{$key}.form.right",
                function ($model) use ($key) {
                    echo view(
                        'cms::components.taxonomies',
                        [
                            'postType' => $key,
                            'model' => $model,
                        ]
                    )->render();
                }
            );
        }
    }

    public function addAdminScripts()
    {
        $ver = Version::getVersion();
        HookAction::enqueueScript('core-vendor', 'jw-styles/juzaweb/js/vendor.min.js', $ver);
        HookAction::enqueueScript('core-table', 'jw-styles/juzaweb/js/juzaweb-table.js', $ver);
        HookAction::enqueueScript('core-list', 'jw-styles/juzaweb/js/list-view.js', $ver);
        HookAction::enqueueScript('core-tinymce', 'jw-styles/juzaweb/tinymce/tinymce.min.js', $ver);
    }

    public function addAdminStyles()
    {
    }

    public function addDatatableSearchFieldTypes()
    {
        $this->addFilter(
            Action::DATATABLE_SEARCH_FIELD_TYPES_FILTER,
            function ($items) {
                $items['text'] = [
                    'view' => view('cms::components.datatable.text_field'),
                ];

                $items['select'] = [
                    'view' => view('cms::components.datatable.select_field'),
                ];

                $items['taxonomy'] = [
                    'view' => view('cms::components.datatable.taxonomy_field'),
                ];
                $items['post'] = [
                    'view' => view('cms::components.datatable.post_field'),
                ];

                return $items;
            }
        );
    }

    public function registerEmailHooks()
    {
        HookAction::registerEmailHook(
            'register_success',
            [
                'label' => trans_cms('cms::app.registered_success'),
                'params' => [
                    'name' => trans_cms('cms::app.user_name'),
                    'email' => trans_cms('cms::app.user_email'),
                    'verifyToken' => trans_cms('cms::app.verify_token'),
                ],
            ]
        );
    }

    public function addConfigs(): void
    {
        $menus = [
            'header_menu' => [
                'label' => 'Header Menu',
            ],
            'footer_menu' => [
                'label' => 'Footer Menu',
            ],
            'sub_footer' => [
                'label' => 'Sub Footer',
            ],
        ];

        set_config('nav_menus', $menus);
    }
}
