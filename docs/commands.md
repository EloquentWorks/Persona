# 🧰 Commands

Persona includes commands for installation and package setup.

## 📦 Install Command

```bash
php artisan persona:install
```

The install command may publish:

- Configuration
- Migrations
- Views

## 📤 Manual Publishing

Publish config:

```bash
php artisan vendor:publish --tag=persona-config
```

Publish migrations:

```bash
php artisan vendor:publish --tag=persona-migrations
```

Publish views:

```bash
php artisan vendor:publish --tag=persona-views
```

## 🗃️ Run Migrations

```bash
php artisan migrate
```

## 🧹 Clear Caches

After changing configuration or views:

```bash
php artisan optimize:clear
```
