# 🎭 Laravel Persona

[![Tests](https://github.com/EloquentWorks/Persona/actions/workflows/tests.yml/badge.svg)](https://github.com/EloquentWorks/Persona/actions/workflows/tests.yml)
[![Latest Release](https://img.shields.io/github/v/release/EloquentWorks/Persona)](https://github.com/EloquentWorks/Persona/releases)
[![License](https://img.shields.io/github/license/EloquentWorks/Persona)](LICENSE)

Elegant public profile tools for Laravel applications.

Laravel Persona gives your Eloquent user model a clean way to manage public profiles, unique usernames, username change tokens, display names, mottos, bios, avatars, banners, social links, custom links, visibility, publishing, comments, and profile view tracking.

```php
$profile = $user->persona()->create([
    'slug' => 'signal-nick',
    'display_name' => 'Nick',
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

## ✨ Highlights

- Public user profiles for Eloquent models
- Slug-based profile URLs
- Unique usernames backed by profile slugs
- Configurable username change tokens
- Username token earning intervals
- Maximum username token balances
- Display names, headlines, mottos, bios, and locations
- Avatar and banner media support
- Website, social links, and custom links
- Public and private profile visibility
- Optional profile publishing controls
- Profile view tracking
- Optional guest comments
- Nested comments with configurable depth
- Query scopes for public, published, and visible profiles
- Publishable views
- Optional built-in profile routes
- Configurable models, table names, route names, storage disk, and validation limits

## 📋 Requirements

| Laravel | PHP | Orchestra Testbench |
| --- | --- | --- |
| 11.15+ | 8.2+ | 9.x |
| 12.x | 8.2+ | 10.x |
| 13.x | 8.3+ | 11.x |

## 📦 Installation

Install the package through Composer:

```bash
composer require eloquent-works/persona
```

Install Persona:

```bash
php artisan persona:install
php artisan migrate
```

Add `HasPersona` to your account model:

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

## 🎭 Profiles

Create a profile:

```php
$profile = $user->persona()->create([
    'slug' => 'john-doe',
    'display_name' => 'John Doe',
    'headline' => 'Laravel Developer',
    'motto' => 'Ship clean code.',
    'bio' => 'I build Laravel applications and packages.',
    'location' => 'Kansas',
    'is_public' => true,
    'published_at' => now(),
]);
```

Read profile URLs and media URLs:

```php
$profile->url();

$profile->avatarUrl();

$profile->bannerUrl();
```

Track profile views:

```php
$profile->recordView();
```

## 🪪 Username Tokens

Persona can limit username changes with tokens. By default, a profile can earn tokens over time and spend them when changing usernames.

```php
$profile->usernameTokens();

$profile->canChangeUsername();

$profile->changeUsername('signal-nick');
```

You can also use helpers on the user model:

```php
$user->personaUsernameTokens();

$user->canChangePersonaUsername();

$user->changePersonaUsername('signal-nick');
```

## 🔎 Query Helpers

```php
use EloquentWorks\Persona\Models\Persona;

Persona::query()->public()->get();

Persona::query()->published()->get();

Persona::query()->visible()->get();
```

## 🔗 Links and Social Profiles

Persona supports profile links and social links.

```php
$profile->update([
    'website_url' => 'https://example.com',
    'links' => [
        [
            'label' => 'GitHub',
            'url' => 'https://github.com/EloquentWorks',
        ],
    ],
    'social_links' => [
        'github' => 'EloquentWorks',
    ],
]);
```

## 💬 Comments

When comments are enabled, profiles can accept comments with configurable depth and guest behavior.

```php
$comment = $profile->comments()->create([
    'body' => 'Great profile!',
    'user_id' => auth()->id(),
]);

$comment->replies()->create([
    'body' => 'Thank you!',
    'user_id' => $profile->user_id,
]);
```

## 🛣️ Routes

Persona can register optional profile routes.

```php
Route::get('/@{persona:slug}', ShowPersonaController::class)
    ->name('persona.show');
```

View a profile:

```php
route('persona.show', $profile);
```

## ⚙️ Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=persona-config
```

Important options:

```php
return [
    'tables' => [
        'profiles' => 'persona_profiles',
        'comments' => 'persona_comments',
        'users' => 'users',
    ],

    'models' => [
        'persona' => EloquentWorks\Persona\Models\Persona::class,
        'comment' => EloquentWorks\Persona\Models\PersonaComment::class,
        'user' => null,
    ],

    'storage' => [
        'disk' => 'public',
    ],

    'routes' => [
        'enabled' => true,
        'show_name' => 'persona.show',
    ],

    'profiles' => [
        'require_published_at' => false,
        'allow_private_profiles' => true,
    ],

    'usernames' => [
        'token_interval_months' => 6,
        'tokens_per_interval' => 1,
        'max_tokens' => 2,
        'token_cost' => 1,
        'unique' => true,
    ],

    'comments' => [
        'enabled' => true,
        'allow_guest_comments' => false,
        'max_depth' => 2,
    ],
];
```

## 🧰 Commands

```bash
php artisan persona:install
```

Publish assets manually:

```bash
php artisan vendor:publish --tag=persona-config
php artisan vendor:publish --tag=persona-migrations
php artisan vendor:publish --tag=persona-views
```

## 📚 Documentation

Full documentation is available in the [docs](docs/README.md) directory:

- [Installation](docs/installation.md)
- [Configuration](docs/configuration.md)
- [Architecture](docs/architecture.md)
- [Profiles](docs/profiles.md)
- [Usernames and Tokens](docs/usernames-and-tokens.md)
- [Links and Media](docs/links-and-media.md)
- [Visibility and Publishing](docs/visibility-and-publishing.md)
- [Comments](docs/comments.md)
- [Routes](docs/routes.md)
- [Views and Blade](docs/views-and-blade.md)
- [Commands](docs/commands.md)
- [Customization](docs/customization.md)
- [Security](docs/security.md)
- [Testing](docs/testing.md)

## ✅ Quality Checks

```bash
composer validate --strict
composer quality
```

Or run the tools separately:

```bash
composer format
composer analyse
composer test
```

## 🔐 Security

Treat public profile input as user-generated content. Validate URLs, escape rendered content, authorize profile edits, and avoid exposing private profile data through search, sitemaps, or public routes.

Security vulnerabilities should be reported privately according to [SECURITY.md](SECURITY.md).

## 🤝 Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) and [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md).

## 📄 License

Laravel Persona is open-source software licensed under the [MIT License](LICENSE).
