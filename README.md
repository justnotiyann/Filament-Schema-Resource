# Filament Schema Resource

Artisan command package to generate **Filament Resource** schema files (Form & Table) automatically based on an Eloquent model.

## Features

- Generate boilerplate **FormSchema** & **TableSchema** classes.
- `--generate` flag: introspect model's table columns and create fields automatically.
- Supports unguarded models (`$guarded = []`), not only `$fillable`.
- Skips common columns: `id`, `created_at`, `updated_at`, `deleted_at`.
- Optional manual registration to Filament Panel.

## 📦 Installation via Packagist

```bash
composer require justnotiyann/filament-schema-resource
```

Package akan otomatis terdiscovery oleh Laravel.

## 🛠️ Usage

```bash
php artisan make:filament-schema-resource Invoice --model=App\Models\Invoice --generate
```

Options:
- `--model=`: jika berbeda dari nama `name`.
- `--force`: overwrite file yang sudah ada.
- `--generate`: auto-generate fields based on model's table.

Contoh hasil generate:

```php
Forms\Components\TextInput::make('amount')->required();
Tables\Columns\TextColumn::make('user_id')->label('User');
```

## ✨ Publishing Stubs (opsional)

Jika kamu ingin customize stub-nya:

```bash
php artisan vendor:publish --tag=filament-schema-stubs
```

Ini akan copy stubs ke `stubs/` folder di proyek Laravel kamu, dan kamu bisa ubah sesuai style.

## 👨‍💻 Development / Install secara lokal

```json
// Tambahkan ke composer.json
"repositories": [{
  "type": "vcs",
  "url": "https://github.com/username/filament-schema-resource"
}]
```

Kemudian:

```bash
composer require justnotiyann/filament-schema-resource:dev-main
```

## 📜 License

MIT License. See `LICENSE.md` for details.
