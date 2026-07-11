<?php

use EloquentWorks\Persona\Http\Controllers\PersonaController;
use EloquentWorks\Persona\Models\Persona;

return [

    /*
    |--------------------------------------------------------------------------
    | Persona Database Tables
    |--------------------------------------------------------------------------
    |
    | These table names are used by Persona when storing public profile data.
    |
    | You may change these values if your application uses custom table names.
    | If you do, make sure your published migrations use the same names.
    |
    */

    'tables' => [
        'profiles' => 'persona_profiles',
        'users' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Persona Models
    |--------------------------------------------------------------------------
    |
    | These are the model classes used by Persona.
    |
    | You may replace the default Persona model with your own custom model if
    | you need to extend profile behavior. The user model may be left as null
    | to use Laravel's configured auth user model.
    |
    */

    'models' => [
        'persona' => Persona::class,
        'user' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Persona Routes
    |--------------------------------------------------------------------------
    |
    | Persona does not load public profile routes automatically. Register them
    | inside your application's routes/web.php file using:
    |
    | Route::persona();
    |
    | These defaults are used by the Route::persona() macro.
    |
    */

    'routes' => [
        'prefix' => '',
        'path' => '@{persona}',
        'middleware' => ['web'],
        'name' => 'persona.',
        'show_name' => 'persona.show',
        'controller' => PersonaController::class,
        'parameter' => 'persona',
    ],

    /*
    |--------------------------------------------------------------------------
    | Persona Usernames
    |--------------------------------------------------------------------------
    |
    | Persona uses the profile slug as the public username. Username changes can
    | be limited with tokens so users cannot rename themselves too often.
    |
    | By default, users can earn one username token every six months and can hold
    | a maximum of two tokens. New profiles do not receive an initial token
    | because their first username is free when the profile is created.
    |
    */

    'usernames' => [
        'enabled' => true,
        'unique' => true,
        'initial_tokens' => 0,
        'token_cost' => 1,
        'tokens_per_interval' => 1,
        'token_interval_months' => 6,
        'max_tokens' => 2,
        'min_length' => 3,
        'max_length' => 32,
        'regex' => '/^[a-z0-9_][a-z0-9_-]*[a-z0-9_]$/',
        'reserved' => [
            'admin',
            'api',
            'dashboard',
            'login',
            'logout',
            'register',
            'settings',
            'support',
            'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Persona Views
    |--------------------------------------------------------------------------
    |
    | These views are used when rendering public profile pages.
    |
    | You may publish the package views and customize them inside your
    | application's resources/views/vendor/persona directory.
    |
    */

    'views' => [
        'show' => 'persona::show',
        'layout' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Persona Storage
    |--------------------------------------------------------------------------
    |
    | This disk is used when generating URLs for stored profile assets such as
    | avatars and banners.
    |
    | The default value uses Laravel's "public" filesystem disk.
    |
    */

    'storage' => [
        'disk' => 'public',
        'avatar_directory' => 'personas/avatars',
        'banner_directory' => 'personas/banners',
    ],

    /*
    |--------------------------------------------------------------------------
    | Persona Slugs
    |--------------------------------------------------------------------------
    |
    | Slugs are used for public profile URLs.
    |
    | For example, a user with the slug "nick" may be available at /@nick.
    | You may customize the source field, separator, and maximum length.
    |
    */

    'slugs' => [
        'source' => 'name',
        'separator' => '-',
        'max_length' => 64,
        'reserved' => [
            'admin',
            'api',
            'dashboard',
            'login',
            'logout',
            'register',
            'settings',
            'support',
            'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Persona Profile Fields
    |--------------------------------------------------------------------------
    |
    | These settings control limits for common public profile fields.
    |
    | Validation rules and form requests may reference these values so users can
    | adjust profile limits without modifying the package source code.
    |
    */

    'fields' => [
        'display_name_max' => 80,
        'headline_max' => 120,
        'bio_max' => 1000,
        'location_max' => 120,
        'website_url_max' => 255,
    ],

    /*
    |--------------------------------------------------------------------------
    | Persona Visibility
    |--------------------------------------------------------------------------
    |
    | These values control the default visibility of newly created profiles.
    |
    | If require_published_at is true, a profile must have a published_at value
    | before it can be publicly visible.
    |
    */

    'visibility' => [
        'default_public' => true,
        'require_published_at' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Persona Links
    |--------------------------------------------------------------------------
    |
    | Persona may store social links and custom links as JSON.
    |
    | You can use these settings to limit how many links a profile may display
    | and which social platforms are officially supported by your application.
    |
    */

    'links' => [
        'max_social_links' => 10,
        'max_custom_links' => 10,

        'allowed_social_platforms' => [
            'github',
            'linkedin',
            'x',
            'youtube',
            'twitch',
            'discord',
            'instagram',
            'facebook',
            'tiktok',
            'website',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Persona Features
    |--------------------------------------------------------------------------
    |
    | These feature flags allow applications to enable or disable optional
    | Persona behavior.
    |
    */

    'features' => [
        'profile_views' => true,
        'social_links' => true,
        'custom_links' => true,
        'metadata' => true,
        'avatars' => true,
        'banners' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Persona Events
    |--------------------------------------------------------------------------
    |
    | This controls whether Persona dispatches profile lifecycle events.
    |
    | Events are useful for notifications, activity feeds, logging, analytics,
    | and other application-specific side effects.
    |
    */

    'dispatch_events' => true,

];
