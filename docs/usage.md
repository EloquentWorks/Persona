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

## Username tokens

Check a profile's token balance:

```php
$profile->usernameTokens();
```

Change a username when the profile has enough tokens:

```php
$profile->changeUsername('signal-nick');
```

Use the user model helpers:

```php
$user->personaUsernameTokens();

$user->canChangePersonaUsername();

$user->changePersonaUsername('signal-nick');
```

Persona will normalize the username, check reserved names, enforce uniqueness, spend the configured token cost, and update the profile slug.

