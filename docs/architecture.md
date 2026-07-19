# 🧱 Architecture

Laravel Persona is designed as a small, focused profile layer for Laravel applications.

## 🎯 Core Concepts

Persona separates profile data from authentication data.

Your `users` table remains responsible for authentication. Persona stores public-facing profile data in dedicated profile tables.

## 🧬 User Trait

The `HasPersona` trait adds profile helpers to your user model.

Common helpers include:

```php
$user->persona;

$user->personaUsernameTokens();

$user->canChangePersonaUsername();

$user->changePersonaUsername('signal-nick');
```

## 🎭 Persona Model

The Persona model stores profile fields such as:

- Slug
- Display name
- Headline
- Motto
- Bio
- Location
- Avatar path
- Banner path
- Website URL
- Social links
- Custom links
- Visibility
- Publishing state
- View counts

## 💬 Comments

Comments are stored separately from profiles and can support nested replies.

Applications can disable comments, require authentication, or enforce moderation at the application layer.

## 🛣️ Routes

Persona may provide built-in public profile routes, or applications may define custom routes and controllers.

## 🖼️ Views

Persona includes publishable views so applications can customize profile pages without replacing the package internals.

## 🔐 Authorization

Persona keeps authorization flexible.

Your application should decide who may:

- Create profiles
- Edit profiles
- Publish profiles
- View private profiles
- Delete comments
- Moderate public content
