# 🚀 Persona v1.1.0 Features

Laravel Persona v1.1.0 adds production-focused profile features while preserving the v1.0.0 API.

## 📊 Detailed Views

```php
$profile->recordDetailedView(
    request: request(),
    viewer: auth()->user(),
    metadata: ['source' => 'profile-page'],
);
```

## 🪪 Username History

```php
$profile->changeUsernameWithHistory(
    username: 'signal-nick',
    changedBy: auth()->user(),
    reason: 'User requested username change.',
);
```

## 🏅 Badges

```php
$profile->awardBadge('verified');

$profile->hasActiveBadge('verified');

$profile->revokeBadge('verified');
```

## ✅ Profile Completeness

```php
$score = $profile->refreshCompleteness();

$checklist = $profile->completionChecklist();
```

## 🧾 SEO

```php
$profile->safeSeoTitle();

$profile->safeSeoDescription();

$profile->socialShareMeta();
```

## 🔐 Validation Rules

```php
use EloquentWorks\Persona\Rules\ReservedPersonaUsername;
use EloquentWorks\Persona\Rules\SafeProfileUrl;

'slug' => ['required', new ReservedPersonaUsername()],
'website_url' => ['nullable', new SafeProfileUrl()],
```

## 🧹 Prune Views

```bash
php artisan persona:prune-views --days=365 --dry-run
php artisan persona:prune-views --days=365 --force
```
