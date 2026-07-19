# 🎨 Customization

Persona is designed to be customized by Laravel applications.

## 🧩 Custom Models

Use configuration to replace models:

```php
'models' => [
    'persona' => App\Models\Profile::class,
    'comment' => App\Models\ProfileComment::class,
],
```

## 🗃️ Custom Table Names

```php
'tables' => [
    'profiles' => 'profiles',
    'comments' => 'profile_comments',
],
```

## 🛣️ Custom Routes

Disable built-in routes:

```php
'routes' => [
    'enabled' => false,
],
```

Register your own:

```php
Route::get('/people/{persona:slug}', ProfileController::class)
    ->name('profiles.show');
```

## 🖼️ Custom Views

Publish the views:

```bash
php artisan vendor:publish --tag=persona-views
```

Then customize files under:

```text
resources/views/vendor/persona
```

## ✅ Custom Validation

Persona keeps validation flexible so applications can define their own rules.

Recommended fields to validate:

- Slug
- Display name
- Headline
- Motto
- Bio
- Location
- Website URL
- Social links
- Custom links
- Avatar file
- Banner file
