# Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=persona-config
```

The published file is located at `config/persona.php`.

## Tables

```php
'tables' => [
    'profiles' => 'persona_profiles',
    'comments' => 'persona_comments',
    'users' => 'users',
],
```

## Models

```php
'models' => [
    'persona' => EloquentWorks\Persona\Models\Persona::class,
    'comment' => EloquentWorks\Persona\Models\PersonaComment::class,
    'user' => null,
],
```

When `user` is `null`, Persona uses the user model configured by Laravel's authentication provider.

## Routes

```php
'routes' => [
    'prefix' => '',
    'path' => '@{persona}',
    'middleware' => ['web'],
    'name' => 'persona.',
    'show_name' => 'persona.show',
    'controller' => EloquentWorks\Persona\Http\Controllers\PersonaController::class,
    'parameter' => 'persona',
],
```

See [Routes](routes.md) for route registration and customization.

## Usernames and tokens

Persona uses the profile `slug` as its public username.

```php
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
```

With the defaults, a profile earns one username token every six months, holds at most two tokens, and spends one token per username change. The initial username created with the profile is free.

## Comments

```php
'comments' => [
    'enabled' => true,
    'require_approval' => false,
    'allow_guest_comments' => false,
    'max_length' => 1000,
    'replies_enabled' => true,
    'max_depth' => 1,
    'soft_deletes' => true,
],
```

Persona supports top-level profile comments and one reply level. See [Comments](comments.md).

> `allow_guest_comments`, `max_depth`, and `soft_deletes` describe package behavior and extension points. Guest posting still requires application-level routing, authentication, and validation.

## Views

```php
'views' => [
    'show' => 'persona::show',
    'layout' => null,
],
```

Publish views with:

```bash
php artisan vendor:publish --tag=persona-views
```

## Storage

```php
'storage' => [
    'disk' => 'public',
    'avatar_directory' => 'personas/avatars',
    'banner_directory' => 'personas/banners',
],
```

## Slugs

```php
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
```

## Field limits

```php
'fields' => [
    'display_name_max' => 80,
    'headline_max' => 120,
    'bio_max' => 1000,
    'location_max' => 120,
    'website_url_max' => 255,
],
```

## Visibility

```php
'visibility' => [
    'default_public' => true,
    'require_published_at' => false,
],
```

## Links

```php
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
```

## Feature flags

```php
'features' => [
    'profile_views' => true,
    'social_links' => true,
    'custom_links' => true,
    'metadata' => true,
    'avatars' => true,
    'banners' => true,
],
```

## Events

```php
'dispatch_events' => true,
```

When enabled, Persona may dispatch its profile lifecycle and view events.
