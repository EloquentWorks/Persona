# ⬆️ Upgrading to Persona v1.1.0

Persona v1.1.0 is additive and release-safe.

## ✅ Keep Existing Migrations

Do not edit:

```text
2026_07_08_000000_create_persona_profiles_table.php
2026_07_11_000001_create_persona_comments_table.php
```

## 🗃️ Add New Migrations

Add:

```text
2026_07_11_000002_create_persona_views_table.php
2026_07_11_000003_create_persona_username_histories_table.php
2026_07_11_000004_create_persona_badges_table.php
2026_07_11_000005_add_persona_v1_1_profile_columns.php
```

Then run:

```bash
php artisan migrate
```

## ⚙️ Config

Add the new config keys from `PATCHES/04-config-persona.md`.

## 🧬 Model Trait

Add `InteractsWithPersonaEnhancements` to the existing `Persona` model, or set the configured Persona model to `AdvancedPersona`.

## ✅ Quality

```bash
composer validate --strict
composer format
composer analyse
composer test
composer quality
```
