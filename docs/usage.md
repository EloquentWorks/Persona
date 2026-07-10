# Usage

Create a profile:

```php
$user->createPersona([
    'display_name' => 'Nick',
    'headline' => 'Laravel Package Builder',
    'bio' => 'Building useful Laravel packages.',
    'published_at' => now(),
]);
```

Update a profile:

```php
$user->updatePersona([
    'bio' => 'Updated bio text.',
]);
```

Get the public URL:

```php
$user->personaUrl();
```
