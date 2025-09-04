<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lees Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the Lees video feature.
    |
    */

    'enabled' => env('LEES_ENABLED', true),

    'max_video_size' => env('LEES_MAX_VIDEO_SIZE', 104857600), // 100MB in bytes

    'max_thumbnail_size' => env('LEES_MAX_THUMBNAIL_SIZE', 10485760), // 10MB in bytes

    'allowed_video_formats' => explode(',', env('LEES_ALLOWED_VIDEO_FORMATS', 'mp4,mov,avi')),

    'allowed_thumbnail_formats' => explode(',', env('LEES_ALLOWED_THUMBNAIL_FORMATS', 'jpeg,png,jpg')),

    'max_hashtags' => env('LEES_MAX_HASHTAGS', 10),

    'max_description_length' => env('LEES_MAX_DESCRIPTION_LENGTH', 500),

    'max_song_length' => env('LEES_MAX_SONG_LENGTH', 100),

    'default_visibility' => env('LEES_DEFAULT_VISIBILITY', 'public'),

    'storage_disk' => env('LEES_STORAGE_DISK', 's3'),

    'video_path' => env('LEES_VIDEO_PATH', 'lees/videos'),

    'thumbnail_path' => env('LEES_THUMBNAIL_PATH', 'lees/thumbnails'),

    'per_page' => env('LEES_PER_PAGE', 20),

    'view_threshold_minutes' => env('LEES_VIEW_THRESHOLD_MINUTES', 60),

];