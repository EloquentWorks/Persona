[![Tests](https://github.com/EloquentWorks/Persona/actions/workflows/tests.yml/badge.svg)](https://github.com/EloquentWorks/Persona/actions/workflows/tests.yml)

# Laravel Persona

Elegant public profile tools for Laravel applications.

Laravel Persona gives your Eloquent user model a clean way to manage public profiles, unique usernames, username change tokens, display names, bios, avatars, banners, social links, custom links, visibility, publishing, and profile view tracking.

```php
$profile = $user->persona;

$profile->recordView();

$profile->avatarUrl();
```

## Supported Versions

| Package Version | PHP | Laravel / Illuminate |
| --- | --- | --- |
| Current | `^8.2` | `^12.0 \|\| ^13.0` |

> Composer will automatically resolve compatible Laravel / Illuminate versions based on your project.

## Installation

Install the package through Composer:

```bash
composer require eloquent-works/persona
```

Publish the config and migrations:

```bash
php artisan persona:install
```

Run your migrations:

```bash
php artisan migrate
```

## Add the trait to your user model

Add `HasPersona` to your application user model:

```php
<?php

namespace App\Models;

use EloquentWorks\Persona\Traits\HasPersona;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasPersona;
}
```

## Features

- Public user profiles
- Slug-based profile URLs
- Unique usernames using the profile slug
- Username change tokens with configurable earning intervals
- Maximum username token balances
- Display names, headlines, bios, and locations
- Avatar and banner support
- Website, social links, and custom links
- Public and private profile visibility
- Published profile controls
- Profile view tracking
- Query scopes for public, published, and visible profiles
- Publishable views
- Configurable model and table names

## Basic usage

```php
$profile = $user->persona()->create([
    'slug' => 'john-doe',
    'display_name' => 'John Doe',
    'headline' => 'Laravel Developer',
    'bio' => 'I build Laravel applications and packages.',
    'is_public' => true,
    'published_at' => now(),
]);

$profile->recordView();

$profile->avatarUrl();

$profile->bannerUrl();

$profile->url();
```

## Username tokens

Laravel Persona can limit username changes with tokens. By default, a profile earns one token every six months and can hold up to two tokens.

```php
$profile->usernameTokens();

$profile->canChangeUsername();

$profile->changeUsername('signal-nick');
```

You can also use the helpers on your user model:

```php
$user->personaUsernameTokens();

$user->canChangePersonaUsername();

$user->changePersonaUsername('signal-nick');
```

## Query helpers

```php
Persona::public()->get();

Persona::published()->get();

Persona::visible()->get();
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=persona-config
```

Important options:

```php
return [
    'tables' => [
        'profiles' => 'persona_profiles',
        'users' => 'users',
    ],

    'models' => [
        'persona' => EloquentWorks\Persona\Models\Persona::class,
        'user' => null,
    ],

    'storage' => [
        'disk' => 'public',
    ],

    'routes' => [
        'show_name' => 'persona.show',
    ],

    'usernames' => [
        'token_interval_months' => 6,
        'tokens_per_interval' => 1,
        'max_tokens' => 2,
        'token_cost' => 1,
        'unique' => true,
    ],
];
```

## Documentation

Full docs are available in the [`docs`](docs) directory:

- [Installation](docs/installation.md)
- [Configuration](docs/configuration.md)
- [Usage](docs/usage.md)
- [Routes](docs/routes.md)
- [Customization](docs/customization.md)
- [Testing](docs/testing.md)

## Security

If you discover a security vulnerability, please report it privately instead of opening a public issue.

## Credits

Built by Eloquent Works.

## License

The MIT License.
