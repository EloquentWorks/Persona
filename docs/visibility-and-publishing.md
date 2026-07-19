# 👁️ Visibility and Publishing

Persona supports public/private visibility and optional publishing.

## 🌍 Public Profiles

Public profiles may be shown to everyone.

```php
$profile->update([
    'is_public' => true,
]);
```

## 🔒 Private Profiles

Private profiles should be hidden from public pages.

```php
$profile->update([
    'is_public' => false,
]);
```

## 📤 Published Profiles

A profile may be considered published when `published_at` is set.

```php
$profile->update([
    'published_at' => now(),
]);
```

## 📝 Draft Profiles

Draft profiles have no publish timestamp.

```php
$profile->update([
    'published_at' => null,
]);
```

## 🔎 Query Scopes

```php
Persona::query()->public();

Persona::query()->published();

Persona::query()->visible();
```

## 🔐 Best Practices

- Do not expose private profiles in public search.
- Do not include private profiles in sitemaps.
- Hide unpublished profiles when publishing is required.
- Let owners preview their own unpublished profile through authorized routes.
