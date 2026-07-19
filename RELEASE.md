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
