### Install Package

```bash
composer require justnotiyann/filament-schema-resource
```

### Update Package

```bash
composer update justnotiyann/filament-schema-resource
```

### Publish Stub

```bash
php artisan vendor:publish --tag=filament-schema-stubs
```

1. ### 🔁 Bagaimana cara agar package nya ngga usah pake dev bisa diinstall

Commit dulu perubahanmu:

```bash
git add .
git commit -m "feat: support auto-detect foreign key relationship"
git push origin main
```

Buat tag versi baru

```bash
git tag v1.1.0 # ubah ini dengan tag terakhir
git push origin v1.1.0 # ubah ini dengan tag terakhir
```

Bagiamana cara agar stubs itu ngga usah di publish.



```
git add .
git commit -m "feat: use internal stub loading, no need to publish"
git tag v1.0.3
git push origin main
git push origin v1.0.3
```
