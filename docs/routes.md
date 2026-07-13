# Routes

Persona does not register public profile routes automatically.

## Register the default route

Add this to `routes/web.php`:

```php
use Illuminate\Support\Facades\Route;

Route::persona();
```

The default route is:

| Method | URI | Name | Controller |
| --- | --- | --- | --- |
| GET | `/@{persona}` | `persona.show` | `PersonaController@show` |

The `{persona}` value is resolved as the profile slug.

## Customize the route

```php
Route::persona([
    'prefix' => 'profiles',
    'path' => '{persona}',
    'name' => 'profiles.',
    'middleware' => ['web'],
]);
```

This produces:

```text
/profiles/{persona}
```

with the route name:

```text
profiles.show
```

## Protect routes

```php
Route::persona([
    'middleware' => ['web', 'auth', 'verified'],
]);
```

Public profiles typically only need `web`, but applications may provide any middleware string or array.

## Use a custom controller

```php
Route::persona([
    'controller' => App\Http\Controllers\ProfileController::class,
]);
```

The controller must provide a `show` method compatible with the generated route:

```php
public function show(string $persona)
{
    // Resolve and display the profile.
}
```

## Generate URLs

```php
route('persona.show', $profile);

$profile->url();

$user->personaUrl();
```

When changing the route name, also update `persona.routes.show_name` so `url()` and `personaUrl()` use the correct route.
