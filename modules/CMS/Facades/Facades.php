<?php


namespace Juzaweb\CMS\Facades;

use Illuminate\Support\Collection;
use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Models\Taxonomy;

class Facades
{
    public static bool $isPostPage = false;

    public static bool $isTaxonomyPage = false;

    public static Post $post;

    public static Taxonomy $taxonomy;

    public static function defaultServiceProviders(): array
    {
        return [
            /*
                 * Laravel Framework Service Providers...
                 */
            \Illuminate\Auth\AuthServiceProvider::class,
            \Illuminate\Broadcasting\BroadcastServiceProvider::class,
            \Illuminate\Bus\BusServiceProvider::class,
            \Illuminate\Cache\CacheServiceProvider::class,
            \Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
            \Illuminate\Cookie\CookieServiceProvider::class,
            \Illuminate\Database\DatabaseServiceProvider::class,
            \Illuminate\Encryption\EncryptionServiceProvider::class,
            \Illuminate\Filesystem\FilesystemServiceProvider::class,
            \Illuminate\Foundation\Providers\FoundationServiceProvider::class,
            \Illuminate\Hashing\HashServiceProvider::class,
            \Illuminate\Mail\MailServiceProvider::class,
            \Illuminate\Notifications\NotificationServiceProvider::class,
            \Illuminate\Pagination\PaginationServiceProvider::class,
            \Illuminate\Pipeline\PipelineServiceProvider::class,
            \Illuminate\Queue\QueueServiceProvider::class,
            \Illuminate\Redis\RedisServiceProvider::class,
            \Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
            \Illuminate\Session\SessionServiceProvider::class,
            \Spatie\TranslationLoader\TranslationServiceProvider::class,
            \Illuminate\Validation\ValidationServiceProvider::class,
            \Illuminate\View\ViewServiceProvider::class,
            /*
                 * Package Service Providers...
                 */
            \Juzaweb\CMS\Providers\CmsServiceProvider::class,
        ];
    }

    public static function defaultFileSystemDisks(): Collection
    {
        return new Collection(
            [
                'local' => [
                    'driver' => 'local',
                    'root'   => storage_path('app'),
                    'throw'  => false,
                ],

                'public' => [
                    'driver'     => 'local',
                    'root'       => storage_path('app/public'),
                    'url'        => env('APP_STORAGE_URL', '/storage'),
                    'visibility' => 'public',
                    'throw'      => false,
                ],

                'backup' => [
                    'driver' => 'local',
                    'root'   => storage_path('app/backups'),
                ],

                'tmp' => [
                    'driver' => 'local',
                    'root'   => storage_path('app/tmps'),
                ],

                's3' => [
                    'driver'                  => 's3',
                    'key'                     => env('AWS_ACCESS_KEY_ID'),
                    'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
                    'region'                  => env('AWS_DEFAULT_REGION'),
                    'bucket'                  => env('AWS_BUCKET'),
                    'url'                     => env('AWS_URL'),
                    'endpoint'                => env('AWS_ENDPOINT'),
                    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
                    'throw'                   => false,
                ],
            ]
        );
    }

    public static function defaultConfigs(): array
    {
        $langs = array_keys(config('app.locales', []));

        $result = [
            'banner'             => [
                'show_api' => true,
            ],
            'user_registration'  => [
                'show_api' => true,
            ],
            'user_verification'  => [
                'show_api' => true,
            ],
            'comment_able'       => [
                'show_api' => true,
            ],
            'comment_type'       => [
                'show_api' => true,
            ],
            'comments_per_page'  => [
                'show_api' => true,
            ],
            'comments_approval'  => [
                'show_api' => true,
            ],
            'author_name'        => [
                'show_api' => true,
            ],
            'google_analytics'   => [
                'show_api' => true,
            ],
            'language'           => [
                'show_api' => true,
            ],
            'timezone'           => [
                'show_api' => true,
            ],
            'date_format'        => [
                'show_api' => true,
            ],
            'date_format_custom' => [
                'show_api' => true,
            ],
            'time_format'        => [
                'show_api' => true,
            ],
            'time_format_custom' => [
                'show_api' => true,
            ],
            'fb_app_id'          => [
                'show_api' => true,
            ],
            'facebook_pixel_id'  => [
                'show_api' => true,
            ],
            'backend_messages'   => [
                'show_api' => false,
            ],
            'socialites'         => [
                'show_api' => false,
            ],
            'posts_per_page'     => [
                'show_api' => true,
            ],
            'posts_per_rss'      => [
                'show_api' => true,
            ],
            'captcha'            => [
                'show_api' => true,
            ],
            'google_captcha'     => [
                'show_api' => false,
            ],
            'map_link'           => [
                'show_api' => false,
            ],
            'mail'               => [
                'show_api' => false,
            ],
            'cache_duration'     => [
                'show_api' => false,
            ],
            'phone'              => [
                'show_api' => false,
            ],
            'fax'                => [
                'show_api' => false,
            ],
            'hosts'              => [
                'show_api' => false,
            ],
            'templates'          => [
                'show_api' => false,
            ],
            'theme_colors'             => [
                'show_api' => false,
            ],
        ];
        foreach ($langs as $lang) {
            $result["title_$lang"]             = [
                'show_api' => true,
            ];
            $result["description_$lang"]       = [
                'show_api' => true,
            ];
            $result["sitename_$lang"]          = [
                'show_api' => true,
            ];
            $result["location_$lang"]          = [
                'show_api' => true,
            ];
            $result["bo_box_$lang"]          = [
                'show_api' => true,
            ];
            $result["site_keywords_$lang"]     = [
                'show_api' => true,
            ];
            $result["logo_$lang"]              = [
                'show_api' => true,
            ];
            $result["white_logo_$lang"]        = [
                'show_api' => true,
            ];
            $result["footer_logo_$lang"]       = [
                'show_api' => true,
            ];
            $result["og_image_default_$lang"]  = [
                'show_api' => true,
            ];
            $result["thumbnail_default_$lang"] = [
                'show_api' => true,
            ];
        }

        return $result;
    }

    public static function defaultImageMimetypes(): array
    {
        return [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'text/plain',
            'video/mp4',
            'video/quicktime',
            'video/webm',
            'application/json',
        ];
    }

    public static function defaultImageExtensions(): array
    {
        return [
            'gif',
            'jpeg',
            'jpg',
            'png',
            'webp',
            'svg',
            'mp4',
            'mov',
            'json',
        ];
    }

    public static function defaultFileExtensions(): array
    {
        return [
            'zip',
            'gif',
            'jpeg',
            'jpg',
            'png',
            'svg',
            'pdf',
            'xml',
            'mp4',
            'mov',
            'mp3',
            'youtube',
            'json',
        ];
    }

    public static function defaultFileMimetypes(): array
    {
        return [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'text/plain',
            'application/pdf',
            'text/xml',
            'video/mp4',
            'video/quicktime',
            'video/webm',
            'audio/mp3',
            'audio/mpeg',
            'application/json',
        ];
    }

    public static function defaultAudioExtensions(): array
    {
        return [
            'mp3',
            'mpeg',
            'wav'
        ];
    }

    public static function defaultURLExtensions(): array
    {
        return [
            'youtube',
        ];
    }

    public static function defaultAudioMimetypes(): array
    {
        return [
            'audio/mp3',
            'audio/mpeg',
            'audio/wav',
            'audio/ogg',
            'audio/flac',
            'audio/aac',
            'audio/m4a',
            'audio/x-ms-wma',
            'audio/webm',
        ];
    }

    public static function defaultVideoExtensions(): array
    {
        return [
            'mp4',
            'mov',
        ];
    }

    public static function defaultVideoMimetypes(): array
    {
        return [
            'video/mp4',
            'video/quicktime',
            'video/webm',
        ];
    }

    public static function defaultSVGMimetypes(): array
    {
        return [
            'image/svg+xml',
            'text/plain'
        ];
    }

    public static function defaultTwigBridgeEnabled(): array
    {
        return [
            'TwigBridge\Extension\Laravel\Event',
            'TwigBridge\Extension\Loader\Facades',
            'TwigBridge\Extension\Loader\Filters',
            'TwigBridge\Extension\Loader\Functions',
            'Juzaweb\CMS\Extension\Globals',

            //'TwigBridge\Extension\Laravel\Session',
            //'TwigBridge\Extension\Laravel\Model',
            //'TwigBridge\Extension\Laravel\Gate',

            //'TwigBridge\Extension\Laravel\Form',
            //'TwigBridge\Extension\Laravel\Html',
            //'TwigBridge\Extension\Laravel\Legacy\Facades',

            'Juzaweb\CMS\Extension\Url',
            'Juzaweb\CMS\Extension\Dump',
            'Juzaweb\CMS\Extension\Input',
            'Juzaweb\CMS\Extension\Translator',
            'Juzaweb\CMS\Extension\Str',
        ];
    }

    public static function defaultTwigFacades(): Collection
    {
        return collect(
            [
                'JWQuery' => JWQuery::class,
            ]
        );
    }
}
