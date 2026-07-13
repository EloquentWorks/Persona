# Customization

Persona can be customized through its configuration, models, views, routes, and application-level validation.

## Publish configuration

```bash
php artisan vendor:publish --tag=persona-config
```

## Publish views

```bash
php artisan vendor:publish --tag=persona-views
```

Published views are placed in:

```text
resources/views/vendor/persona
```

You may also change the view used by the package controller:

```php
'views' => [
    'show' => 'profiles.show',
    'layout' => 'layouts.app',
],
```

## Custom Persona model

Create a model that extends the package model:

```php
<?php

namespace App\Models;

use EloquentWorks\Persona\Models\Persona as BasePersona;

class Persona extends BasePersona
{
    // Application-specific behavior.
}
```

Register it in `config/persona.php`:

```php
'models' => [
    'persona' => App\Models\Persona::class,
],
```

## Custom comment model

```php
<?php

namespace App\Models;

use EloquentWorks\Persona\Models\PersonaComment as BasePersonaComment;

class PersonaComment extends BasePersonaComment
{
    // Application-specific behavior.
}
```

Register it:

```php
'models' => [
    'comment' => App\Models\PersonaComment::class,
],
```

## Custom user model

```php
'models' => [
    'user' => App\Models\User::class,
],
```

When left as `null`, Persona uses Laravel's configured authentication user model.

## Custom tables

Change table names before running migrations:

```php
'tables' => [
    'profiles' => 'profiles',
    'comments' => 'profile_comments',
    'users' => 'users',
],
```

If migrations have already run, create an application migration to rename or modify the existing tables.

## Custom public route

```php
Route::persona([
    'prefix' => 'members',
    'path' => '{persona}',
    'name' => 'members.',
    'middleware' => ['web'],
    'controller' => App\Http\Controllers\MemberProfileController::class,
]);
```

Update the configured route name when using a custom name:

```php
'routes' => [
    'show_name' => 'members.show',
],
```

## Custom slug source

Use a model attribute:

```php
'slugs' => [
    'source' => 'name',
],
```

A model using `HasPersona` may also customize the fallback value used during slug generation:

```php
public function personaSlugSource(): string
{
    return (string) $this->username;
}
```

## Validation

Persona exposes configurable field, username, comment, and link limits. Applications should use those values in form requests and UI validation:

```php
'display_name' => [
    'nullable',
    'string',
    'max:'.config('persona.fields.display_name_max', 80),
],
```

## Events

Set:

```php
'dispatch_events' => true,
```

to allow Persona's profile lifecycle and view events to be dispatched. Register listeners in the consuming Laravel application as usual.
