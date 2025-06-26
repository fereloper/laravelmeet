<?php

namespace LaravelMeet\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelMeet extends Facade {
    protected static function getFacadeAccessor() {
        return 'laravelmeet';
    }
}

// config/laravelmeet.php
return [
    'driver' => env('LARAVEL_MEET_DRIVER', 'zoom'),
    
    'zoom' => [
        'jwt' => env('ZOOM_JWT'),
    ],
];
