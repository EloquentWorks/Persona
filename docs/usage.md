# Usage

## Create a profile

```php
$profile = $user->createPersona([
    'display_name' => 'Nick',
    'headline' => 'Laravel Package Builder',
    'bio' => 'Building useful Laravel packages.',
    'location' => 'Kansas',
    'website_url' => 'https://example.com',
    'is_public' => true,
    'published_at' => now(),
]);
```

When no `slug` is supplied, Persona generates one from the configured slug source.

## Access a profile

```php
$profile = $user->persona;

$user->hasPersona();
```

## Update a profile

```php
$profile = $user->updatePersona([
    'headline' => 'Open-source Laravel Developer',
    'bio' => 'Updated profile biography.',
]);
```

If the user does not already have a profile, `updatePersona()` creates one.

## Public profile URL

```php
$url = $user->personaUrl();

$url = $profile->url();
```

The named profile route must be registered before calling these helpers.

## Avatar and banner URLs

```php
$avatarUrl = $profile->avatarUrl();

$bannerUrl = $profile->bannerUrl();
```

Pass a filesystem disk when needed:

```php
$avatarUrl = $profile->avatarUrl('s3');
```

## Profile views

```php
$profile->recordView();

$views = $profile->profile_views;
```

The package profile controller records a view when `persona.features.profile_views` is enabled.

## Query scopes

```php
use EloquentWorks\Persona\Models\Persona;

$publicProfiles = Persona::public()->get();

$publishedProfiles = Persona::published()->get();

$visibleProfiles = Persona::visible()->get();
```

## Username tokens

Check the current balance:

```php
$tokens = $profile->usernameTokens();
```

Check whether a username change is available:

```php
$profile->canChangeUsername();

$user->canChangePersonaUsername();
```

Get the next token grant date:

```php
$nextGrant = $profile->nextUsernameTokenAt();
```

Change the username:

```php
$profile->changeUsername('signal-nick');

$user->changePersonaUsername('signal-nick');
```

Persona normalizes the username, validates its format, rejects reserved names, enforces uniqueness when enabled, and spends the configured token cost.

For an administrative or migration-driven change that should not spend a token:

```php
$profile->changeUsername('signal-nick', spendToken: false);
```

## Social, custom, and metadata values

The following attributes are cast to arrays:

```php
$profile->social_links = [
    'github' => 'https://github.com/example',
];

$profile->custom_links = [
    [
        'label' => 'Portfolio',
        'url' => 'https://example.com',
    ],
];

$profile->metadata = [
    'theme' => 'dark',
];

$profile->save();
```

Applications are responsible for validating and rendering these values.

## Comments

```php
$comment = $profile->addComment($user, 'Great profile.');

$reply = $comment->addReply($otherUser, 'I agree.');
```

See [Comments](comments.md) for approval, pinning, editing, deletion, and reply queries.
