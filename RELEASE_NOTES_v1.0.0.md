# 🎭 Laravel Persona v1.0.0 🎉

The first stable release of Laravel Persona.

Laravel Persona provides elegant public profile tools for Laravel applications, allowing Eloquent user models to own customizable public profiles with slugs, display names, usernames, avatars, banners, links, visibility controls, publishing, comments, and profile view tracking.

This release focuses on profile customization, clean public URLs, username management, visibility safety, publishable views, and a polished Laravel package experience.

## ✨ Features

- Public user profiles
- `HasPersona` user trait
- Slug-based profile URLs
- Unique username support through profile slugs
- Configurable username change tokens
- Username token earning intervals
- Maximum username token balances
- Display names, headlines, mottos, bios, and locations
- Avatar URL helper
- Banner URL helper
- Website links
- Social links
- Custom links
- Public and private profile visibility
- Optional profile publishing controls
- Profile view tracking
- Query scopes for public profiles
- Query scopes for published profiles
- Query scopes for visible profiles
- Optional profile comments
- Nested comment replies
- Configurable comment depth
- Optional guest comment support
- Publishable profile views
- Optional built-in profile routes
- Configurable route names
- Configurable storage disk
- Configurable models and table names
- Laravel Pint setup
- PHPStan/Larastan setup
- PHPUnit/Testbench setup
- GitHub Actions workflow
- Documentation folder with icon-led headings

## 📦 Installation

Install Laravel Persona through Composer:

```bash
composer require eloquent-works/persona
```

Install the package:

```bash
php artisan persona:install
php artisan migrate
```

## 🧬 User Model Setup

Add `HasPersona` to your user model:

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
    'slug' => 'signal-nick',
    'display_name' => 'Nick',
    'headline' => 'Laravel Developer',
    'motto' => 'Ship clean code.',
    'bio' => 'I build Laravel applications and packages.',
    'is_public' => true,
    'published_at' => now(),
]);
```

Read useful URLs:

```php
$profile->url();
$profile->avatarUrl();
$profile->bannerUrl();
```

Record a profile view:

```php
$profile->recordView();
```

## 🪪 Username Tokens

Persona can control username changes through configurable tokens.

```php
$profile->usernameTokens();

$profile->canChangeUsername();

$profile->changeUsername('signal-nick');
```

User model helpers are also available:

```php
$user->personaUsernameTokens();

$user->canChangePersonaUsername();

$user->changePersonaUsername('signal-nick');
```

## 🔐 Visibility and Publishing

Profiles may be public, private, published, unpublished, or filtered through the visible query scope.

```php
Persona::query()->public()->get();

Persona::query()->published()->get();

Persona::query()->visible()->get();
```

## 💬 Comments

Laravel Persona supports optional comments and nested replies.

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

## 📚 Documentation

The documentation includes icon-led guides for:

- Installation
- Configuration
- Architecture
- Profiles
- Usernames and tokens
- Links and media
- Visibility and publishing
- Comments
- Routes
- Views and Blade
- Commands
- Customization
- Security
- Testing

## ✅ Supported Versions

- Laravel 11.15+ with PHP 8.2+
- Laravel 12 with PHP 8.2+
- Laravel 13 with PHP 8.3+

## ⬆️ Upgrading

Laravel Persona v1.0.0 is the first stable release.

No upgrade steps are required when installing v1.0.0 into a new application.

If upgrading from a pre-release version:

```bash
composer require eloquent-works/persona:^1.0
php artisan vendor:publish --tag=persona-migrations
php artisan migrate
php artisan vendor:publish --tag=persona-config
php artisan optimize:clear
```

## 🧰 Quality Checks

Before deploying, run:

```bash
composer validate --strict
composer format
composer analyse
composer test
```

Or run the complete quality suite:

```bash
composer quality
```

## 🔐 Security

Treat all profile fields, links, and comments as user-generated content.

Recommended security practices:

- Authorize profile edits.
- Validate profile slugs.
- Validate user-provided URLs.
- Escape rendered profile content.
- Hide private profiles from public routes.
- Hide unpublished profiles when publishing is required.
- Keep profile media on an appropriate storage disk.
- Protect destructive actions with policies or gates.

Security vulnerabilities should be reported privately according to `SECURITY.md`.

## ❤️ Thank You

Thank you for using Laravel Persona.

Feedback, bug reports, feature requests, documentation improvements, and pull requests are always welcome.
