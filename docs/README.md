[![tests](https://github.com/EloquentWorks/Persona/actions/workflows/tests.yml/badge.svg)](https://github.com/EloquentWorks/Persona/actions/workflows/tests.yml)

# Persona

Elegant public profile tools for Laravel applications.

Persona gives your Eloquent user model a clean way to manage public profiles, slugs, display names, bios, avatars, banners, social links, custom links, visibility, publishing, and profile view tracking.

```php
$profile = $user->persona;

$profile->recordView();

$profile->avatarUrl();
```

## Features

- Public user profiles
- Slug-based profile URLs
- Display names, headlines, bios, and locations
- Avatar and banner support
- Website, social links, and custom links
- Public and private profile visibility
- Published profile controls
- Profile view tracking
- Query scopes for public, published, and visible profiles
- Publishable views
- Configurable model and table names

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13
- A user model that extends Eloquent

## Installation

Install the package through Composer:

```bash
composer require eloquentworks/persona
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
