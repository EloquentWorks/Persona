# 💬 Comments

Persona can support profile comments and nested replies.

## ✅ Enable Comments

```php
'comments' => [
    'enabled' => true,
    'allow_guest_comments' => false,
    'max_depth' => 2,
],
```

## ➕ Create a Comment

```php
$comment = $profile->comments()->create([
    'body' => 'Great profile!',
    'user_id' => auth()->id(),
]);
```

## 📥 Retrieve Comments

Retrieve all comments for a profile:

```php
$comments = $profile->comments()
    ->latest()
    ->get();
```

Retrieve approved top-level comments only:

```php
$comments = $profile->comments()
    ->approved()
    ->topLevel()
    ->latest()
    ->get();
```

Retrieve top-level comments with their approved replies:

```php
$comments = $profile->comments()
    ->approved()
    ->topLevel()
    ->with([
        'replies' => fn ($query) => $query
            ->approved()
            ->oldest(),
    ])
    ->latest()
    ->get();
```

Paginate comments for a public profile page:

```php
$comments = $profile->comments()
    ->approved()
    ->topLevel()
    ->with([
        'replies' => fn ($query) => $query
            ->approved()
            ->oldest(),
    ])
    ->latest()
    ->paginate(15);
```

Retrieve only replies:

```php
$replies = $profile->comments()
    ->repliesOnly()
    ->approved()
    ->oldest()
    ->get();
```

Count approved top-level comments:

```php
$count = $profile->comments()
    ->approved()
    ->topLevel()
    ->count();
```

## ↩️ Reply to a Comment

```php
$comment->replies()->create([
    'body' => 'Thank you!',
    'user_id' => $profile->user_id,
]);
```

## 🧱 Depth Limits

Use `max_depth` to limit deeply nested discussions.

```php
'max_depth' => 2,
```

## 👥 Guest Comments

Guest comments should be disabled by default.

```php
'allow_guest_comments' => false,
```

## 🛡️ Moderation

Applications should moderate profile comments when they are publicly visible.

Recommended protections:

- Rate limiting
- Spam detection
- Abuse reporting
- Author blocking
- Staff moderation
- HTML escaping
