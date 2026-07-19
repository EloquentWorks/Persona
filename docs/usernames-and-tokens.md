# 🪪 Usernames and Tokens

Persona can treat the profile slug as a username and limit username changes using tokens.

## 🎟️ Token Concept

A username token represents permission to change a username.

This prevents users from constantly changing public profile URLs.

## ⚙️ Configuration

```php
'usernames' => [
    'token_interval_months' => 6,
    'tokens_per_interval' => 1,
    'max_tokens' => 2,
    'token_cost' => 1,
    'unique' => true,
],
```

## 🔎 Check Tokens

```php
$profile->usernameTokens();
```

## ✅ Check If Username Can Change

```php
$profile->canChangeUsername();
```

## ✏️ Change Username

```php
$profile->changeUsername('signal-nick');
```

## 👤 User Helpers

```php
$user->personaUsernameTokens();

$user->canChangePersonaUsername();

$user->changePersonaUsername('signal-nick');
```

## 🔐 Safety Tips

- Keep usernames unique.
- Validate allowed characters.
- Reserve system usernames such as `admin`, `login`, `register`, and `api`.
- Consider redirecting old profile URLs if your application needs stable profile links.
