# 🛣️ Routes

Persona can register public profile routes or let your application define its own.

## ✅ Built-In Routes

Example route shape:

```php
Route::get('/@{persona:slug}', ShowPersonaController::class)
    ->name('persona.show');
```

## ⚙️ Route Configuration

```php
'routes' => [
    'enabled' => true,
    'prefix' => '',
    'middleware' => ['web'],
    'show_name' => 'persona.show',
],
```

## 🔗 Generate Profile URLs

```php
route('persona.show', $profile);

$profile->url();
```

## 🚫 Disable Built-In Routes

```php
'routes' => [
    'enabled' => false,
],
```

Then define your own route:

```php
Route::get('/users/{persona:slug}', ShowProfileController::class)
    ->name('profiles.show');
```

## 🔐 Route Safety

Public profile routes should use visible-profile checks.

Do not allow private or unpublished profiles to be shown unless the current user is authorized.
