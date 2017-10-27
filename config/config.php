<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Scan
    |--------------------------------------------------------------------------
    |
    | The paths we scan for controllers
    |
    */
    'scan' => [
        'enabled' => true,
        'paths' => [
            base_path('app/Http/Controllers/*')
        ]
    ],
    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Here is the config for setting up caching feature.
    |
    */

    'cache' => [
        'enabled' => true,
        'prefix' => 'ohrana',
        'lifetime' => 3600,
    ],
    /*
    |--------------------------------------------------------------------------
    | Repositories
    |--------------------------------------------------------------------------
    |
    | Here we set the repositories we want to use to fetch and save permissions
    | and roles
    |
    */

    'repositories' => [
        'permissions'   => 'stoykov\Ohrana\Repositories\DoctrinePermissions',
        'roles'         => 'stoykov\Ohrana\Repositories\DoctrineRoles',
    ],

    'user_model' => 'App\Models\User',

];