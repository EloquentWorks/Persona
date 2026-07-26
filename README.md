# 👤 Laravel Persona

[![Tests](https://github.com/EloquentWorks/Persona/actions/workflows/tests.yml/badge.svg)](https://github.com/EloquentWorks/Persona/actions/workflows/tests.yml)
[![Latest Release](https://img.shields.io/github/v/release/EloquentWorks/Persona)](https://github.com/EloquentWorks/Persona/releases)
[![License](https://img.shields.io/github/license/EloquentWorks/Persona)](LICENSE)

Elegant, customizable public profiles for Laravel applications.

Laravel Persona gives an Eloquent user model profile pages, unique public usernames, display names, headlines, mottos, biographies, avatars, banners, social links, custom links, visibility controls, publishing, view tracking, profile comments, completeness scoring, badges, and convenient model helpers.

```php
$profile = $user->createPersona([
    'display_name' => 'Nick',
    'headline' => 'Laravel Package Builder',
    'motto' => 'Build useful things.',
    'bio' => 'Building useful Laravel packages.',
    'location' => 'Kansas',
    'is_public' => true,
    'published_at' => now(),
]);

$url = $user->personaUrl();
$score = $user->personaCompletenessScore();
```

## 📋 Supported Versions

| Package version | PHP | Laravel / Illuminate |
|---|---:|---:|
| Current | `^8.2` | `^12.0 \|\| ^13.0` |

Composer resolves the compatible Illuminate packages for the consuming Laravel application.

## 🚀 Installation

Install Persona:

```bash
composer require eloquent-works/persona
```

Publish the configuration and migrations:

```bash
php artisan persona:install
```

Run the migrations:

```bash
php artisan migrate
```

Add `HasPersona` to the application user model:

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

See [Installation](docs/installation.md) for publishing options and setup guidance.

## ✨ Features

- Public or private user profiles
- Slug-based public usernames and route model binding
- Configurable username-change tokens
- Reserved-name, format, length, and uniqueness checks
- Display names, headlines, mottos, biographies, and locations
- Avatar and banner URLs through Laravel filesystems
- Website URLs, social links, and custom links
- Publishing and configurable visibility requirements
- Public, published, and visible query scopes
- Profile view counters
- Profile comments with replies, approval, pinning, editing, and deletion
- Profile completeness scoring
- Custom profile badge awarding
- Lifecycle events for creation, updates, publishing, unpublishing, and views
- Publishable views and customizable public routes
- Configurable models, tables, field limits, feature flags, and storage
- PHPUnit, PHPStan/Larastan, Laravel Pint, and Composer quality scripts

## 🚀 Quick Start

### Create a profile

```php
$profile = $user->createPersona([
    'display_name' => 'Nick',
    'headline' => 'Laravel Package Builder',
    'motto' => 'Build useful things.',
    'bio' => 'Building useful Laravel packages.',
    'location' => 'Kansas',
    'website_url' => 'https://example.com',
    'is_public' => true,
    'published_at' => now(),
]);
```

### Read or update the profile

```php
$profile = $user->persona;

$user->hasPersona();

$user->updatePersona([
    'headline' => 'Open-source Laravel Developer',
]);
```

### Register the public profile route

Persona does not register public routes automatically.

```php
use Illuminate\Support\Facades\Route;

Route::persona();
```

The default route format is:

```text
/@{persona}
```

### Generate URLs

```php
$profile->url();
$profile->avatarUrl();
$profile->bannerUrl();

$user->personaUrl();
```

## 👁️ Visibility and Publishing

```php
$profile->isVisible();

$publicProfiles = Persona::public()->get();
$publishedProfiles = Persona::published()->get();
$visibleProfiles = Persona::visible()->get();
```

Visibility follows:

```php
config('persona.visibility.require_published_at');
```

When `require_published_at` is disabled, a public profile may be visible without a publication timestamp. When enabled, the profile must be public and have a past `published_at` value.

## 🏷️ Username Tokens

Persona uses the profile slug as its public username.

```php
$profile->usernameTokens();
$profile->canChangeUsername();
$profile->nextUsernameTokenAt();
$profile->usernameIsAvailable('signal-nick');
$profile->changeUsername('signal-nick');
```

Use the helpers on the user model:

```php
$user->personaUsernameTokens();
$user->canChangePersonaUsername();
$user->changePersonaUsername('signal-nick');
```

An administrative change can skip token spending:

```php
$profile->changeUsername(
    'signal-nick',
    spendToken: false,
);
```

Persona normalizes the username and applies the configured length, regular expression, reserved-name, uniqueness, and token rules.

## 📊 Completeness

Refresh the profile's completeness score directly:

```php
$score = $profile->refreshCompleteness();
```

Or through the user model:

```php
$score = $user->personaCompletenessScore();
```

The user helper returns `0` when no Persona profile exists.

See [Completeness and Badges](docs/completeness-and-badges.md).

## 🏅 Badges

Award a badge directly through the profile:

```php
$badge = $profile->awardBadge(
    'package-builder',
    [
        'label' => 'Package Builder',
        'description' => 'Published a Laravel package.',
    ],
    $user,
);
```

Or use the user-model helper:

```php
$badge = $user->awardPersonaBadge(
    'package-builder',
    [
        'label' => 'Package Builder',
    ],
);
```

The helper returns `null` when the user does not have a Persona profile.

## 💬 Profile Comments

```php
$comment = $profile->addComment(
    $user,
    'Great profile.',
);

$reply = $comment->addReply(
    $otherUser,
    'Thank you.',
);
```

Moderate and edit comments:

```php
$comment->approve();
$comment->unapprove();

$comment->pin();
$comment->unpin();

$comment->edit('Updated comment.');
$comment->delete();
```

Retrieve common comment groups:

```php
$profile->approvedComments()->get();
$profile->pinnedComments()->get();

PersonaComment::topLevel()->approved()->get();
PersonaComment::repliesOnly()->get();
PersonaComment::pinned()->get();
```

Persona provides the model API. The consuming application remains responsible for routes, request validation, authorization, rate limiting, spam controls, and guest identity handling.

See [Profile Comments](docs/comments.md).

## 📣 Events

Persona can dispatch:

- `PersonaCreated`
- `PersonaUpdated`
- `PersonaPublished`
- `PersonaUnpublished`
- `PersonaViewed`

Disable lifecycle events globally:

```php
'dispatch_events' => false,
```

See [Events](docs/events.md).

## ⚙️ Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=persona-config
```

Major configuration groups include:

```php
return [
    'tables' => [],
    'models' => [],
    'routes' => [],
    'usernames' => [],
    'views' => [],
    'storage' => [],
    'slugs' => [],
    'fields' => [],
    'comments' => [],
    'visibility' => [],
    'links' => [],
    'features' => [],
    'dispatch_events' => true,
];
```

See [Configuration](docs/configuration.md) for the complete reference.

## ✅ Quality Checks

Run all package checks:

```bash
composer quality
```

Or run them separately:

```bash
composer format
composer format:test
composer analyse
composer test
```

Validate Composer metadata before a release:

```bash
composer validate --strict
```

The quality pipeline should complete with zero formatting, PHPStan, or PHPUnit failures.

See [Testing and Quality](docs/testing.md).

## 📚 Documentation

- [Documentation Index](docs/README.md)
- [Installation](docs/installation.md)
- [Configuration](docs/configuration.md)
- [Usage](docs/usage.md)
- [Completeness and Badges](docs/completeness-and-badges.md)
- [Profile Comments](docs/comments.md)
- [Routes](docs/routes.md)
- [Events](docs/events.md)
- [Customization](docs/customization.md)
- [Security](docs/security.md)
- [Testing and Quality](docs/testing.md)

## 🔐 Security

Treat every public profile field, URL, link, comment, metadata value, and badge attribute as user-generated or administrator-generated content.

Validate URLs, escape rendered values, authorize profile and comment changes, rate-limit public write endpoints, and avoid exposing private profiles through search, sitemaps, APIs, or public routes.

Security vulnerabilities should be reported privately according to [SECURITY.md](SECURITY.md).

## 🤝 Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) and [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md).

## 🙏 Credits

Built by Eloquent Works.

## 📄 License

Laravel Persona is open-source software licensed under the [MIT License](LICENSE).
