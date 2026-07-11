# Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=persona-config
```

Persona supports custom tables, models, route defaults, views, storage disks, slug settings, field limits, visibility defaults, link limits, feature flags, and events.

## Username tokens

Persona uses the profile `slug` as the public username. Username changes can be limited with tokens so users cannot rename themselves too often.

```php
'usernames' => [
    'enabled' => true,
    'unique' => true,
    'initial_tokens' => 0,
    'token_cost' => 1,
    'tokens_per_interval' => 1,
    'token_interval_months' => 6,
    'max_tokens' => 2,
    'min_length' => 3,
    'max_length' => 32,
],
```

With these defaults, users earn one username token every six months and can hold a maximum of two tokens.

