# 🔐 Security

Persona stores and renders public-facing user profile data.

Treat every profile field as untrusted user input.

## ✅ Recommended Defaults

```php
'profiles' => [
    'require_published_at' => true,
    'allow_private_profiles' => true,
],

'comments' => [
    'enabled' => true,
    'allow_guest_comments' => false,
    'max_depth' => 2,
],
```

## 🛡️ Authorize Profile Changes

Only authorized users should edit profiles.

Use policies, gates, or controller checks before profile updates.

## 🔗 Validate Links

Validate website, social, and custom URLs.

Recommended rules:

- Allow `http` and `https`.
- Reject unsafe schemes.
- Escape link labels.
- Add safe `rel` attributes.

## 🖼️ Protect Media Uploads

For avatar and banner uploads:

- Validate file type.
- Validate file size.
- Store on the configured disk.
- Avoid exposing private storage paths directly.

## 👁️ Protect Private Profiles

Private profiles should not appear in:

- Public routes
- Search indexes
- Sitemaps
- API responses
- Public profile lists

## 📝 Protect Draft Profiles

If `require_published_at` is enabled, profiles with `published_at = null` should be hidden from public visitors.

## 💬 Moderate Comments

When comments are enabled, consider:

- Rate limiting
- Spam checks
- Report buttons
- Moderation queues
- Abuse prevention
- HTML escaping
