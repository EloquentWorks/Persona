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
