# 🖼️ Views and Blade

Persona includes publishable views for public profile pages.

## 📤 Publish Views

```bash
php artisan vendor:publish --tag=persona-views
```

Published views are placed in:

```text
resources/views/vendor/persona
```

## 🎨 Customize Profile Pages

You can customize:

- Layout
- Avatar display
- Banner display
- Display name
- Headline
- Motto
- Bio
- Location
- Website link
- Social links
- Custom links
- Comments
- Empty states

## 🔐 Escape Output

Use escaped Blade output for user-generated content:

```blade
{{ $profile->display_name }}
{{ $profile->headline }}
{{ $profile->bio }}
```

Only render raw HTML when your application sanitizes it first.

## 🔗 External Links

For public links, consider:

```blade
<a href="{{ $url }}" rel="nofollow noopener noreferrer" target="_blank">
    {{ $label }}
</a>
```
