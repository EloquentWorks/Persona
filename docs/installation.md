# Installation

## Requirements

- PHP 8.2 or newer
- Laravel 11, 12, or 13
- An Eloquent user model

## Install through Composer

```bash
composer require eloquent-works/persona
```

## Publish package files

Publish the configuration and migrations:

```bash
php artisan persona:install
```

Optionally publish the package views:

```bash
php artisan persona:install --views
```

Optionally publish the route snippet:

```bash
php artisan persona:install --routes
```

Use `--force` to overwrite previously published files:

```bash
php artisan persona:install --force
```

Run the migrations:

```bash
php artisan migrate
```

## Add the trait

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

## Register public profile routes

Persona does not register public routes automatically. Add the route macro to `routes/web.php`:

```php
use Illuminate\Support\Facades\Route;

Route::persona();
```

The default public profile URL is:

```text
/@{username}
```

Continue with [Usage](usage.md) or [Routes](routes.md).
