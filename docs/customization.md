# Customization

You may customize Persona by publishing the config, views, and route snippet.

Use a custom model:

```php
'models' => [
    'persona' => App\Models\Persona::class,
],
```

Use a custom controller:

```php
Route::persona([
    'controller' => App\Http\Controllers\ProfileController::class,
]);
```
