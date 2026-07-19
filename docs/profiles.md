# 🎭 Profiles

Profiles are the main resource in Laravel Persona.

## ➕ Create a Profile

```php
$profile = $user->persona()->create([
    'slug' => 'signal-nick',
    'display_name' => 'Nick',
    'headline' => 'Laravel Developer',
    'motto' => 'Ship clean code.',
    'bio' => 'I build Laravel applications and packages.',
    'location' => 'Kansas',
    'is_public' => true,
    'published_at' => now(),
]);
```

## ✏️ Update a Profile

```php
$profile->update([
    'display_name' => 'Nick',
    'headline' => 'Laravel Package Developer',
    'bio' => 'Building reusable Laravel tools.',
]);
```

## 🔗 Profile URL

```php
$profile->url();
```

## 🖼️ Media URLs

```php
$profile->avatarUrl();

$profile->bannerUrl();
```

## 👁️ Record Views

```php
$profile->recordView();
```

## 🔎 Query Profiles

```php
Persona::query()->public()->get();

Persona::query()->published()->get();

Persona::query()->visible()->get();
```

## 🧹 Recommended Validation

Applications should validate:

- Slug format
- Display name length
- Bio length
- Website URL
- Social links
- Uploaded avatar files
- Uploaded banner files
