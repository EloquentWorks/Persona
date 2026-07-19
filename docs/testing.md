# ✅ Testing

Persona includes PHPUnit and Orchestra Testbench support.

## 🧪 Run Tests

```bash
composer test
```

## 🔎 Run Static Analysis

```bash
composer analyse
```

## 🎨 Run Pint

```bash
composer format
```

Check formatting without changing files:

```bash
vendor/bin/pint --test
```

## 🧰 Run Everything

```bash
composer quality
```

## 📋 Recommended Coverage

Good Persona tests should cover:

- Trait relationships
- Profile creation
- Profile updates
- Public/private visibility
- Published/unpublished visibility
- URL generation
- Avatar URL generation
- Banner URL generation
- View tracking
- Username token calculation
- Username changes
- Username uniqueness
- Comment creation
- Reply creation
- Max comment depth
- Guest comment restrictions
- Configurable models
- Configurable table names
- Route rendering
- View publishing
