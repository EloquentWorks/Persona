# 📦 Installation

This guide explains how to install Laravel Persona.

## ✅ Requirements

| Laravel | PHP | Orchestra Testbench |
| --- | --- | --- |
| 11.15+ | 8.2+ | 9.x |
| 12.x | 8.2+ | 10.x |
| 13.x | 8.3+ | 11.x |

## 📥 Install with Composer

```bash
composer require eloquent-works/persona
```

## 🧰 Run the Installer

```bash
php artisan persona:install
```

The installer publishes the configuration and migrations.

## 🗃️ Run Migrations

```bash
php artisan migrate
```

## 🧬 Add the Trait

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

## ✅ Verify Installation

Create a profile:

```php
$user->persona()->create([
    'slug' => 'signal-nick',
    'display_name' => 'Nick',
    'is_public' => true,
    'published_at' => now(),
]);
```

Load it:

```php
$user->persona;
```
