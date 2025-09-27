<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure all the settings for file uploads in your application.
    |
    */

    'max_file_size' => env('UPLOAD_MAX_FILE_SIZE', '10240'), // 10MB in KB
    
    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ],

    'upload_path' => storage_path('app/public/uploads'),
    
    'max_width' => 2048,  // Maximum image width
    'max_height' => 2048, // Maximum image height
    
    'image_quality' => 90 // JPEG quality
];
