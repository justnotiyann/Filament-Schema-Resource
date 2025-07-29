
# 📦 Filament Schema Resource Generator

Generate `FormSchema` dan `TableSchema` untuk resource Filament langsung dari Eloquent model — lengkap dengan deteksi foreign key secara otomatis (misal: `cabang_id` ➝ relasi `belongsTo` ke model `Cabang`).

---

## ✅ Install Package

```bash
composer require justnotiyann/filament-schema-resource
```

> Tidak perlu publish stub. Semua stub sudah terbundle dalam package ini.

---

## 🚀 Cara Pakai

Buat FormSchema dan TableSchema berdasarkan model:

```bash
php artisan make:filament-schema-resource Invoice --model=SalesInvoice
```

Atau jika hanya ingin generate schema-nya saja:

```bash
php artisan make:filament-schema-resource Invoice --model=SalesInvoice --generate
```

---

## 🔖 Update Package

```bash
composer update justnotiyann/filament-schema-resource
```

---

## 🛠️ Commit dan Tag Versi Baru

```bash
git add .
git commit -m "feat: support auto-detect foreign key relationship"
git push origin main

# Buat tag versi baru
git tag v1.1.0
git push origin v1.1.0
```

---

## 🧼 Opsional: Clear Cache

```bash
php artisan clear-compiled
php artisan config:clear
composer dump-autoload
```
