# Installation

Install Persona through Composer:

```bash
composer require eloquent-works/persona
```

Run the install command:

```bash
php artisan persona:install
```

Run your migrations:

```bash
php artisan migrate
```

Add `HasPersona` to your user model:

```php
use EloquentWorks\Persona\Traits\HasPersona;

class User extends Authenticatable
{
    use HasPersona;
}
```
