# 🎭 Laravel Persona Documentation

Welcome to the Laravel Persona documentation.

Persona gives Laravel applications elegant public profile tools with slugs, display names, usernames, profile media, links, visibility, publishing, comments, and profile view tracking.

## 📚 Guides

- [📦 Installation](installation.md)
- [⚙️ Configuration](configuration.md)
- [🧱 Architecture](architecture.md)
- [🎭 Profiles](profiles.md)
- [🪪 Usernames and Tokens](usernames-and-tokens.md)
- [🔗 Links and Media](links-and-media.md)
- [👁️ Visibility and Publishing](visibility-and-publishing.md)
- [💬 Comments](comments.md)
- [🛣️ Routes](routes.md)
- [🖼️ Views and Blade](views-and-blade.md)
- [🧰 Commands](commands.md)
- [🎨 Customization](customization.md)
- [🔐 Security](security.md)
- [✅ Testing](testing.md)

## 🚀 Quick Start

```bash
composer require eloquent-works/persona
php artisan persona:install
php artisan migrate
```

Add the trait:

```php
use EloquentWorks\Persona\Traits\HasPersona;

class User extends Authenticatable
{
    use HasPersona;
}
```

Create a profile:

```php
$user->persona()->create([
    'slug' => 'signal-nick',
    'display_name' => 'Nick',
    'is_public' => true,
    'published_at' => now(),
]);
```

## 🧭 Recommended Reading Order

1. Installation
2. Configuration
3. Profiles
4. Visibility and Publishing
5. Routes
6. Views and Blade
7. Security
8. Testing
