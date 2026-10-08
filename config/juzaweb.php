<?php

use Juzaweb\CMS\Facades\Facades;

return [
    /**
     * Admin url prefix
     *
     * Default: admin-cp
     */
    'admin_prefix' => env('ADMIN_PREFIX', ''),


    'adminbar' => [
        /**
         * Show admin-bar in frontend
         *
         * Default: true
         */
        'enable' => (bool) env('ADMINBAR_ENDABLE', false),
    ],

    /**
     * Cache prefix
     *
     * Default: progmix_
     */
    'cache_prefix' => 'progmix_',

    /**
     * Show logs in admin page
     */
    'logs_viewer' => env('JW_LOGS_VIEWER', true),

    'translation' => [
        /**
         * Enable translation CMS/Plugins/Themes
         */
        'enable' => env('JW_ENABLE_TRANSLATE', true)
    ],

    'email' => [
        /**
         * Method send email
         *
         * Support: sync, queue, cron
         * Default: sync
         */
        'method' => env('JW_MAIL_METHOD', 'sync'),

        'default' => [
            'driver' => env('MAIL_MAILER'),
            'host' => env('MAIL_HOST'),
            'port' => env('MAIL_PORT'),
            'from_address' => env('MAIL_FROM_ADDRESS'),
            'from_name' => env('MAIL_FROM_NAME'),
            'encryption' => env('MAIL_ENCRYPTION'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'verify_peer' => false,
        ],
    ],

    'notification' => [
        /**
         * Method send notification
         *
         * Support: sync, queue, cron
         * Default: sync
         */
        'method' => env('JW_NOTIFICATION_METHOD', 'sync'),

        /**
         * Send mail via
         *
         * Support: database, mail
         */
        'via' => [
            'database' => [
                'enable' => true,
            ],
            'mail' => [
                'enable' => true,
                'connection' => 'default',
            ]
        ]
    ],

    'theme' => [
        /**
         * Enable upload themes
         *
         * Default: true
         */
        'enable_upload' => false,

        /**
         * Themes path
         *
         * This path used for save the generated theme. This path also will added
         * automatically to list of scanned folders.
         */
        'path' => JW_THEME_PATH,
    ],

    'plugin' => [
        /**
         * Enable upload plugins
         *
         * Default: true
         */
        'enable_upload' => false,

        /**
         * Path plugins folder
         *
         * Default: plugins
         */
        'path' => JW_PLUGIN_PATH,

        /**
         * Plugins assets path
         *
         * Path for assets when it was published
         * Default: plugins
         */
        'assets' => public_path('plugins'),
    ],

    'performance' => [
        /**
         * Minify views when compile
         *
         * Default: true
         */
        'minify_views' => true,

        /**
         * Deny iframe to website
         *
         * Default: true
         */
        'deny_iframe' => (bool) env('JW_DENY_IFRAME', true),

        'query_cache' => [
            /**
             * Enable query cache (Only frontend)
             *
             * Default: true
             */
            'enable' => env('JW_QUERY_CACHE', true),

            /**
             * Query cache driver
             *
             * Default: file
             */
            'driver' => env('JW_QUERY_CACHE_DRIVER', 'file'),

            /**
             * Query cache lifetime
             *
             * Default: 3600 (1 hour)
             */
            'lifetime' => env('JW_QUERY_CACHE_LIFETIME', 3600),
        ],
    ],

    /**
     * File management setting
     */
    'filemanager' => [
        /**
         * FileSystem disk
         *
         * Default: public
         */
        'disk' => 'public',

        /**
         * Enable upload from url
         *
         * Default: true
         */
        'upload_from_url' => (bool) env('UPLOAD_FROM_URL', true),

        /**
         * Optimizer image after upload
         *
         * @see https://juzaweb.com/documentation/start/image-optimizer
         */
        'image-optimizer' => (bool) env('IMAGE_OPTIMIZER', false),
        'image-quality' => 95,

        'svg_mimetypes' => [
            ...Facades::defaultSVGMimetypes(),
            //
        ],

        /**
         * Server Side Image Resizer
         *
         * Default: true
         */
        'image_resizer' => env('JW_IMAGE_RESIZER', false),

        /**
         * File type
         *
         * Default: file, image
         */
        'types' => [
            'file' => [
                /**
                 * Max file size upload
                 *
                 * Default: 50 (MB)
                 */
                'max_size' => env('JW_MEDIA_FILE_MAX_SIZE', 50),
                'valid_mime' => [
                    ...Facades::defaultFileMimetypes(),
                    //
                ],
                'extensions' => [
                    ...Facades::defaultFileExtensions(),
                    //
                ],
            ],
            'audio' => [
                /**
                 * Max file size upload
                 *
                 * Default: 50 (MB)
                 */
                'max_size' => env('JW_MEDIA_FILE_MAX_SIZE', 50),
                'valid_mime' => [
                    ...Facades::defaultAudioMimetypes(),
                    //
                ],
                'extensions' => [
                    ...Facades::defaultAudioExtensions(),
                    //
                ],
            ],
            'video' => [
                /**
                 * Max file size upload
                 *
                 * Default: 50 (MB)
                 */
                'max_size' => env('JW_MEDIA_FILE_MAX_SIZE', 50),
                'valid_mime' => [
                    ...Facades::defaultVideoMimetypes(),
                    //
                ],
                'extensions' => [
                    ...Facades::defaultVideoExtensions(),
                    //
                ],
            ],
            'url' => [
                /**
                 * Max file size upload
                 *
                 * Default: 50 (MB)
                 */
                'max_size' => env('JW_MEDIA_FILE_MAX_SIZE', 50),
                'valid_mime' => [
                    ...Facades::defaultFileMimetypes(),
                    //
                ],
                'extensions' => [
                    ...Facades::defaultURLExtensions(),
                    //
                ],
            ],
            'image' => [
                /**
                 * Max image size upload
                 *
                 * Default: 25 (MB)
                 */
                'max_size' => env('JW_MEDIA_IMAGE_MAX_SIZE', default: 25),

                'valid_mime' => [
                    ...Facades::defaultImageMimetypes(),
                    //
                ],
                'extensions' => [
                    ...Facades::defaultImageExtensions(),
                    //
                ],
            ],

        ],
    ],

    'api' => [
        'enable' => env('JW_ALLOW_API', false),

        /**
         * Frontend API configs
         */
        'frontend' => [
            'enable' => env('JW_ALLOW_FRONTEND_API', env('JW_ALLOW_API', false)),
        ]
    ],

    /**
     * Default database config
     */
    'config' => array_merge(
        Facades::defaultConfigs(),
        [
            //
        ]
    ),
];
