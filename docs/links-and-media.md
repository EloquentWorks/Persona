# 🔗 Links and Media

Persona supports public profile links and profile media.

## 🌐 Website URL

```php
$profile->update([
    'website_url' => 'https://example.com',
]);
```

## 🔗 Custom Links

```php
$profile->update([
    'links' => [
        [
            'label' => 'GitHub',
            'url' => 'https://github.com/EloquentWorks',
        ],
    ],
]);
```

## 📣 Social Links

```php
$profile->update([
    'social_links' => [
        'github' => 'EloquentWorks',
        'x' => 'example',
    ],
]);
```

## 🖼️ Avatar

```php
$profile->avatarUrl();
```

## 🏞️ Banner

```php
$profile->bannerUrl();
```

## 🔐 URL Security

Validate every user-provided URL.

Recommended rules:

- Allow only `http` and `https`.
- Reject `javascript:` URLs.
- Escape labels when rendering.
- Consider adding `rel="nofollow noopener noreferrer"` to external links.
