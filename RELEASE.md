# 🚀 Release Checklist

Use this checklist before tagging a Laravel Persona release.

## ✅ Pre-release Checks

Run the quality suite:

```bash
composer validate --strict
composer format
composer analyse
composer test
composer quality
```

Confirm the repository is clean:

```bash
git status
```

## 📚 Documentation

Before every release, review:

- `README.md`
- `CHANGELOG.md`
- `UPGRADING.md`
- `SECURITY.md`
- `CONTRIBUTING.md`
- `docs/README.md`
- All topic-specific documentation in `docs/`

Make sure examples match the current public API.

## 🧪 Manual Smoke Test

In a fresh Laravel application, verify that you can:

- Install the package.
- Publish config, migrations, and views.
- Run migrations.
- Add `HasPersona` to the user model.
- Create a profile.
- View a public profile route.
- Hide a private profile.
- Hide an unpublished profile when publishing is required.
- Change a username using tokens.
- Record profile views.
- Render avatar and banner URLs.
- Create comments when comments are enabled.
- Reject guest comments when guest comments are disabled.

## 🏷️ Tagging

```bash
git checkout main
git pull --ff-only

composer validate --strict
composer quality

git add .
git commit -m "Prepare Laravel Persona v1.0.0 release"
git push

git tag -a v1.0.0 -m "Laravel Persona v1.0.0"
git push origin v1.0.0
```

## 📦 GitHub Release

Create a GitHub release using:

```text
Tag: v1.0.0
Title: Laravel Persona v1.0.0 🎭
Body: Paste RELEASE_NOTES_v1.0.0.md
```

## 📣 Packagist

After tagging:

- Confirm Packagist picked up the release.
- Confirm the package install command works.
- Confirm the README renders correctly.

# Release Process

This document describes the release workflow for Laravel Persona.

## Before releasing

Verify that all of the following pass successfully:

``` bash
composer test
composer analyse
vendor/bin/pint --test
```

Ensure that:

-   Documentation has been updated.
-   CHANGELOG (if used) reflects the release.
-   Version constraints are correct.
-   `composer.json` metadata is up to date.
-   README examples match the current API.

## Create a release

1.  Commit all changes.
2.  Create a version tag.

``` bash
git tag v1.0.0
git push origin v1.0.0
```

3.  Create a GitHub Release using the new tag.
4.  Packagist will automatically detect the new release if GitHub
    integration is enabled.

## Release checklist

-   PHPUnit passes
-   PHPStan passes
-   Laravel Pint passes
-   No debug code remains
-   Documentation reviewed
-   New features include tests
-   Public API reviewed for backwards compatibility

## Versioning

Persona follows Semantic Versioning.

-   **MAJOR** --- breaking changes
-   **MINOR** --- new backwards-compatible features
-   **PATCH** --- bug fixes and documentation improvements

Examples:

-   `1.0.0`
-   `1.1.0`
-   `1.1.1`
-   `2.0.0`

## After release

-   Verify the package appears on Packagist.
-   Verify installation from a fresh Laravel project.
-   Announce the release if desired.
