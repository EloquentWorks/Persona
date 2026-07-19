# ⚙️ Configuration

Persona is configured through `config/persona.php`.

## 📤 Publish Configuration

```bash
php artisan vendor:publish --tag=persona-config
```

## 🗃️ Tables

```php
'tables' => [
    'profiles' => 'persona_profiles',
    'comments' => 'persona_comments',
    'users' => 'users',
],
```

## 🧩 Models

```php
'models' => [
    'persona' => EloquentWorks\Persona\Models\Persona::class,
    'comment' => EloquentWorks\Persona\Models\PersonaComment::class,
    'user' => null,
],
```

Set `user` when your application uses a custom user model that cannot be inferred.

## 💾 Storage

```php
'storage' => [
    'disk' => 'public',
],
```

The storage disk is used for profile media such as avatars and banners.

## 🛣️ Routes

```php
'routes' => [
    'enabled' => true,
    'prefix' => '',
    'middleware' => ['web'],
    'show_name' => 'persona.show',
],
```

Disable built-in routes if your application registers its own profile pages.

## 👁️ Profiles

```php
'profiles' => [
    'require_published_at' => false,
    'allow_private_profiles' => true,
],
```

Use `require_published_at` when drafts should stay hidden until explicitly published.

## 🪪 Usernames

```php
'usernames' => [
    'token_interval_months' => 6,
    'tokens_per_interval' => 1,
    'max_tokens' => 2,
    'token_cost' => 1,
    'unique' => true,
],
```

## 💬 Comments

```php
'comments' => [
    'enabled' => true,
    'allow_guest_comments' => false,
    'max_depth' => 2,
],
```

## 🔐 Recommended Defaults

For most public applications:

```php
'profiles' => [
    'require_published_at' => true,
    'allow_private_profiles' => true,
],

'comments' => [
    'enabled' => true,
    'allow_guest_comments' => false,
    'max_depth' => 2,
],
```
