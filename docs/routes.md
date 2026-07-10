# Routes

Persona does not register public routes automatically.

Add this to `routes/web.php`:

```php
use Illuminate\Support\Facades\Route;

Route::persona();
```

Customize the route:

```php
Route::persona([
    'prefix' => 'profiles',
    'path' => '{persona}',
    'name' => 'profiles.',
    'middleware' => ['web'],
]);
```
