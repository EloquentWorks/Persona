# Testing

## Install dependencies

```bash
composer install
```

## Run PHPUnit

```bash
composer test
```

Or run PHPUnit directly:

```bash
vendor/bin/phpunit
```

## Static analysis

```bash
composer analyse
```

This runs PHPStan with the package's `phpstan.neon` configuration.

## Code formatting

Format the package:

```bash
composer format
```

Check formatting without changing files:

```bash
vendor/bin/pint --test
```

## Run an individual test

```bash
vendor/bin/phpunit --filter PersonaCommentTest
```

## Test coverage

Persona's tests should cover:

- profile creation and updates
- slug generation
- username token earning and spending
- profile visibility and route resolution
- profile view recording
- top-level comments and replies
- two-level reply enforcement
- approval and pinning
- editing and soft deletion
- force deletion of comment threads
- package installation and publishing

## Before opening a pull request

Run:

```bash
composer test
composer analyse
vendor/bin/pint --test
```

All commands should pass before submitting changes.
