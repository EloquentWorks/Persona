# Customization

Persona can be customized through configuration, replacement models, views, routes, and application-level validation.

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

## Custom Persona model

```php
<?php

namespace App\Models;

use EloquentWorks\Persona\Models\Persona as BasePersona;

class Persona extends BasePersona
{
    // Application-specific behavior.
}
```

Register it:

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

## Validation responsibilities

Persona exposes configurable limits for profile fields, usernames, comments, and links. The consuming application should use those values in form requests.

Persona does not automatically enforce:

- profile field limits on arbitrary model assignment
- social or custom link limits
- guest comment access
- comment authorization
- rate limiting or spam protection

## Comment behavior

Persona supports one reply level and uses soft deletes. These behaviors are part of the package's current model design rather than runtime configuration switches.

Applications may replace the comment model when deeper customization is required.

## Custom route

```php
Route::persona([
    'prefix' => 'members',
    'path' => '{persona}',
    'name' => 'members.',
    'middleware' => ['web'],
    'controller' => App\Http\Controllers\MemberProfileController::class,
]);
```

When changing the route name, update:

```php
'routes' => [
    'show_name' => 'members.show',
],
```
