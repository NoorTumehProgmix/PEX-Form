<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'storage_server'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => \Juzaweb\CMS\Facades\Facades::defaultFileSystemDisks()->merge(
        [
            'private' => [
                'driver' => 'local',
                'root' => storage_path('app/private'),
            ],

            'passport' => [
                'driver' => 'local',
                'root' => storage_path('app/passport'),
            ],

            'storage_server' => [
                'driver' => 'custom',
                'base_url' => env('CUSTOM_STORAGE_BASE_URL'),
                'api_key' => env('CUSTOM_STORAGE_API_KEY'),
                'images_url' => env('CUSTOM_STORAGE_IMAGE_URL'),

            ],
            'azure' => [
                'driver'    => 'azure',
                'name'      => env('AZURE_STORAGE_NAME'),
                'key'       => env('AZURE_STORAGE_KEY'),
                'container' => env('AZURE_STORAGE_CONTAINER'),
                'url'       => env('AZURE_STORAGE_URL'),
                'prefix'    => null,
            ],
        ]
    )->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
