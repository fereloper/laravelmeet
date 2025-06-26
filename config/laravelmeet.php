<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Default Meeting Provider
    |--------------------------------------------------------------------------
    |
    | Supported: "zoom", "google", "jitsi", "microsoft"
    |
    */
    
    'driver' => env('LARAVEL_MEET_DRIVER', 'zoom'),
    
    /*
    |--------------------------------------------------------------------------
    | Zoom Configuration
    |--------------------------------------------------------------------------
    */
    
    'zoom' => [
        'jwt' => env('ZOOM_JWT'),
        
        // Optional: If you want to use OAuth instead of JWT
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        'api_url' => env('ZOOM_API_URL', 'https://api.zoom.us/v2'),
        'redirect_uri' => env('ZOOM_REDIRECT_URI'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Google Meet (Coming Soon)
    |--------------------------------------------------------------------------
    */
    
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
        'calendar_id' => env('GOOGLE_CALENDAR_ID'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Jitsi (Self-hosted / no auth needed)
    |--------------------------------------------------------------------------
    */
    
    'jitsi' => [
        'domain' => env('JITSI_DOMAIN', 'meet.jit.si'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Microsoft Teams (Coming Soon)
    |--------------------------------------------------------------------------
    */
    
    'microsoft' => [
        'client_id' => env('MS_CLIENT_ID'),
        'client_secret' => env('MS_CLIENT_SECRET'),
        'tenant_id' => env('MS_TENANT_ID'),
        'redirect_uri' => env('MS_REDIRECT_URI'),
    ],
    
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

];
