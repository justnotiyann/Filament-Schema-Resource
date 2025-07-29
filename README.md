# Filament Schema Resource Generator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/justnotiyann/filament-schema-resource.svg?style=flat-square)](https://packagist.org/packages/justnotiyann/filament-schema-resource)
[![Total Downloads](https://img.shields.io/packagist/dt/justnotiyann/filament-schema-resource.svg?style=flat-square)](https://packagist.org/packages/justnotiyann/filament-schema-resource)

Artisan command to generate Filament form & table schema files based on Eloquent model with separate schema classes for better organization.

## Features

- 🚀 Generate Filament resources with separate FormSchema and TableSchema classes
- 🔄 Auto-generate form fields and table columns based on model structure
- 📁 Clean separation of concerns with organized file structure
- ⚡ Force overwrite existing files option
- 🎯 Customizable model relationships handling

## Installation

You can install the package via composer:

```bash
composer require justnotiyann/filament-schema-resource
```

Publish the stub files:

```bash
php artisan vendor:publish --tag=filament-schema-stubs
```

## Usage

### Basic Usage

Generate a basic Filament resource:

```bash
php artisan make:filament-schema-resource Post
```

This will create:
- `app/Filament/Resources/PostResource.php`
- `app/Filament/Resources/PostResource/Pages/ListPost.php`
- `app/Filament/Resources/PostResource/Pages/CreatePost.php`
- `app/Filament/Resources/PostResource/Pages/EditPost.php`
- `app/Filament/Resources/PostResource/Schemas/PostFormSchema.php`
- `app/Filament/Resources/PostResource/Schemas/PostTableSchema.php`

### Advanced Usage

#### Generate with different model name

```bash
php artisan make:filament-schema-resource PostResource --model=Article
```

#### Auto-generate based on model structure

```bash
php artisan make:filament-schema-resource Post --generate
```

This will analyze your `Post` model and automatically generate form fields and table columns based on the database schema.

#### Force overwrite existing files

```bash
php artisan make:filament-schema-resource Post --force
```

### Generated File Structure

```
app/Filament/Resources/
├── PostResource.php
└── PostResource/
    ├── Pages/
    │   ├── ListPost.php
    │   ├── CreatePost.php
    │   └── EditPost.php
    └── Schemas/
        ├── PostFormSchema.php
        └── PostTableSchema.php
```

### Example Generated Schema Classes

#### FormSchema Example

```php
<?php

namespace App\Filament\Resources\PostResource\Schemas;

use Filament\Forms;

class PostFormSchema
{
    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->label(__('Title'))
                ->required(),

            Forms\Components\Select::make('category_id')
                ->label(__('Category'))
                ->relationship('category', 'id')
                ->preload()
                ->searchable()
                ->native(false)
                ->required(),
        ];
    }
}
```

#### TableSchema Example

```php
<?php

namespace App\Filament\Resources\PostResource\Schemas;

use Filament\Tables;

class PostTableSchema
{
    public static function schema(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->label(__('Title')),

            Tables\Columns\TextColumn::make('category_id')
                ->label(__('Category Id')),
        ];
    }
}
```

## Requirements

- PHP 8.1+
- Laravel 10.x or 11.x
- Filament 3.x

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [justnotiyann](https://github.com/justnotiyann)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
