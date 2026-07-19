# ⬆️ Upgrading Laravel Persona

This guide explains how to upgrade Laravel Persona between versions.

## ⬆️ From pre-release to v1.0.0

Laravel Persona v1.0.0 is the first stable release.

Update the package:

```bash
composer require eloquent-works/persona:^1.0
```

Publish the latest migrations:

```bash
php artisan vendor:publish --tag=persona-migrations
```

Run migrations:

```bash
php artisan migrate
```

Publish the latest configuration:

```bash
php artisan vendor:publish --tag=persona-config
```

Publish views if your application customizes profile pages:

```bash
php artisan vendor:publish --tag=persona-views
```

Clear cached configuration and views:

```bash
php artisan optimize:clear
```

## ⚙️ Configuration Review

Review these options after upgrading:

```php
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
```

## 🔐 Security Review

Before deploying an upgrade, confirm that:

- Profile edit routes require authentication.
- Profile update actions are authorized.
- Private profiles are not exposed by public routes.
- Unpublished profiles are hidden when publishing is required.
- User-provided URLs are validated.
- Rendered bios, mottos, links, and comments are escaped.
