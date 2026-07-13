# Usage

## Create a profile

```php
$profile = $user->createPersona([
    'display_name' => 'Nick',
    'headline' => 'Laravel Package Builder',
    'motto' => 'Build useful things.',
    'bio' => 'Building useful Laravel packages.',
    'location' => 'Kansas',
    'website_url' => 'https://example.com',
    'is_public' => true,
    'published_at' => now(),
]);
```

## Check visibility

```php
$profile->isVisible();

$visibleProfiles = Persona::visible()->get();
```

Visibility follows `persona.visibility.require_published_at`.

## Access and update a profile

```php
$profile = $user->persona;

$user->hasPersona();

$user->updatePersona([
    'headline' => 'Open-source Laravel Developer',
]);
```

## Public profile URL

```php
$url = $profile->url();

$url = $user->personaUrl();
```

## Avatar and banner URLs

```php
$avatarUrl = $profile->avatarUrl();

$bannerUrl = $profile->bannerUrl();
```

## Username tokens

```php
$tokens = $profile->usernameTokens();

$profile->canChangeUsername();

$profile->nextUsernameTokenAt();

$profile->changeUsername('signal-nick');
```

Skip token spending for an administrative change:

```php
$profile->changeUsername(
    'signal-nick',
    spendToken: false
);
```

## Comments

```php
$comment = $profile->addComment(
    $user,
    'Great profile.'
);

$reply = $comment->addReply(
    $otherUser,
    'Thank you.'
);
```

Edit a comment:

```php
$comment->edit('Updated comment.');
```

Approve, pin, or delete:

```php
$comment->approve();

$comment->pin();

$comment->delete();
```

See [Profile Comments](comments.md) for the complete comment API.

## Application validation

Configuration values such as profile field limits, link limits, allowed social platforms, and guest-comment preferences are intended for the consuming application's form requests and UI.

Persona does not automatically validate arbitrary profile assignments or register comment-management routes.
