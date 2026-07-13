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

## Visibility

```php
'visibility' => [
    'default_public' => true,
    'require_published_at' => false,
],
```

When `require_published_at` is `false`, a profile is visible when `is_public` is `true`.

When `require_published_at` is `true`, a profile must also have a `published_at` value that is not in the future.

Both `Persona::visible()` and `$persona->isVisible()` honor this option.

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

With these defaults, a profile earns one username token every six months, holds at most two tokens, and spends one token per username change.

## Comments

```php
'comments' => [
    'enabled' => true,
    'require_approval' => false,
    'allow_guest_comments' => false,
    'max_length' => 1000,
    'replies_enabled' => true,
],
```

Persona provides model-level comment behavior. Applications are still responsible for routes, request validation, authorization, rate limiting, spam protection, and guest handling.

`allow_guest_comments` is intended for consuming applications and user interfaces. Persona does not automatically create or authenticate guest commenters.

Persona supports top-level comments and one reply level. Comment records use soft deletes.

## Profile field limits

```php
'fields' => [
    'display_name_max' => 80,
    'headline_max' => 120,
    'motto_max' => 160,
    'bio_max' => 1000,
    'location_max' => 120,
    'website_url_max' => 255,
],
```

These values are intended for application form requests and interfaces. Persona does not automatically validate arbitrary model assignments.

Example form request rules:

```php
public function rules(): array
{
    return [
        'display_name' => [
            'nullable',
            'string',
            'max:'.config('persona.fields.display_name_max', 80),
        ],
        'headline' => [
            'nullable',
            'string',
            'max:'.config('persona.fields.headline_max', 120),
        ],
        'motto' => [
            'nullable',
            'string',
            'max:'.config('persona.fields.motto_max', 160),
        ],
        'bio' => [
            'nullable',
            'string',
            'max:'.config('persona.fields.bio_max', 1000),
        ],
    ];
}
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

These limits are intended for application validation and user interfaces. Assigning arrays directly to the model does not automatically enforce them.

Example validation:

```php
use Illuminate\Validation\Rule;

public function rules(): array
{
    return [
        'social_links' => [
            'nullable',
            'array',
            'max:'.config('persona.links.max_social_links', 10),
        ],
        'social_links.*.platform' => [
            'required',
            'string',
            Rule::in(config('persona.links.allowed_social_platforms', [])),
        ],
        'social_links.*.url' => [
            'required',
            'url',
        ],
        'custom_links' => [
            'nullable',
            'array',
            'max:'.config('persona.links.max_custom_links', 10),
        ],
    ];
}
```

## Storage

```php
'storage' => [
    'disk' => 'public',
    'avatar_directory' => 'personas/avatars',
    'banner_directory' => 'personas/banners',
],
```

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
