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

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'cloudinary' => [
            'driver'         => 'cloudinary',
            'api_key'        => env('CLOUDINARY_API_KEY'),
            'api_secret'     => env('CLOUDINARY_API_SECRET'),
            'cloud_name'     => env('CLOUDINARY_CLOUD_NAME'),
            'secure'         => env('CLOUDINARY_SECURE', true),
            'resource_types' => [
                'image' => [
                    'png',
                    'jpeg',
                    'jpg',
                ],
                'video' => [
                    'mp4',
                    'avi',
                    'mp3',
                    'flac',
                ],
                'raw'   => [
                    'pdf',
                    'xlsx',
                    'csv',
                    'txt',
                ],
            ],
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'sample' => [
            'driver' => 'local',
            'root' => app_path('Storage/samples'),
            'url' => env('APP_URL') . '/samples',
            'visibility' => 'private',
        ],

        'attachedFile' => [
            'driver'         => 'cloudinary',
            'api_key'        => env('CLOUDINARY_API_KEY'),
            'api_secret'     => env('CLOUDINARY_API_SECRET'),
            'cloud_name'     => env('CLOUDINARY_CLOUD_NAME'),
            'secure'         => env('CLOUDINARY_SECURE', true),
            'resource_types' => [
                'image' => [
                    'png',
                    'jpeg',
                    'jpg',
                ],
                'video' => [
                    'mp4',
                    'avi',
                    'mp3',
                    'flac',
                ],
                'raw'   => [
                    'pdf',
                    'xlsx',
                    'csv',
                    'txt',
                ],
            ],
        ],

        'dbDump' => [
            'driver' => 'local',
            'root' => storage_path('app/dbDumps'),
            'url' => env('APP_URL') . '/dbDumps',
            'visibility' => 'private',
        ],

    ],

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
        // public_path('avatars') => storage_path('app/avatars'),
        // public_path('projectCoverImages') => storage_path('app/projectCoverImages'),
    ],

];
