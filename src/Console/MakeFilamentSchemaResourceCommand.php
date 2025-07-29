<?php

namespace Iyan\FilamentSchemaResource\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MakeFilamentSchemaResourceCommand extends Command
{
    protected $signature = 'make:filament-schema-resource
                            {name : The name of the resource}
                            {--model= : Model name if different from resource}
                            {--force : Overwrite existing files}
                            {--generate : Auto-generate schema based on model}';

    protected $description = 'Create a Filament resource with separate FormSchema and TableSchema classes';

    protected const BASE_PATH = 'app/Filament/Resources';

    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $modelName = Str::studly($this->option('model') ?? $name);
        $modelClass = "App\\Models\\{$modelName}";

        if (! $this->option('force')) {
            $existing = [
                base_path(self::BASE_PATH . "/{$name}Resource.php"),
                base_path(self::BASE_PATH . "/{$name}Resource/Schemas/{$name}FormSchema.php"),
                base_path(self::BASE_PATH . "/{$name}Resource/Schemas/{$name}TableSchema.php"),
            ];
            foreach ($existing as $file) {
                if ($this->files->exists($file)) {
                    $this->error("❌ File already exists at: {$file}");
                    return Command::FAILURE;
                }
            }
        }

        // Check if stubs are published
        if (!$this->stubsExist()) {
            $this->error('❌ Stubs not found. Please publish them first:');
            $this->line('php artisan vendor:publish --tag=filament-schema-stubs');
            return Command::FAILURE;
        }

        // Generate all files
        $this->generateFormSchema($name, $modelClass);
        $this->generateTableSchema($name, $modelClass);
        $this->generateFilamentResource($name, $modelName);

        $this->components->info("✅ Filament Resource '{$name}' berhasil dibuat.");
        $this->components->bulletList([
            "📁 Resources/{$name}Resource.php",
            "📁 Resources/{$name}Resource/Pages/*.php",
            "📁 Resources/{$name}Resource/Schemas/*.php",
        ]);

        return Command::SUCCESS;
    }

    protected function stubsExist(): bool
    {
        $requiredStubs = [
            'form-schema.stub',
            'table-schema.stub',
            'resource.stub',
            'list-page.stub',
            'create-page.stub',
            'edit-page.stub',
        ];

        foreach ($requiredStubs as $stub) {
            if (!$this->files->exists(base_path("stubs/filament-resource/{$stub}"))) {
                return false;
            }
        }

        return true;
    }

    protected function getModelColumns(string $modelClass): array
    {
        if (! class_exists($modelClass)) {
            $this->warn("⚠️  Model {$modelClass} tidak ditemukan.");
            return [];
        }

        try {
            $model = new $modelClass;
            $table = $model->getTable();
            $connection = $model->getConnectionName() ?? config('database.default');
            $schema = Schema::connection($connection);

            if (!$schema->hasTable($table)) {
                $this->warn("⚠️  Tabel '{$table}' tidak ditemukan.");
                return [];
            }

            $columns = $schema->getColumnListing($table);

            // Exclude common auto-generated columns
            return collect($columns)
                ->reject(fn($col) => in_array($col, ['id', 'created_at', 'updated_at', 'deleted_at']))
                ->values()
                ->all();
        } catch (\Throwable $e) {
            $this->warn("⚠️  Gagal mendapatkan kolom dari model: {$e->getMessage()}");
            return [];
        }
    }

    protected function generateFormSchema(string $name, ?string $modelClass = null): void
    {
        $path = base_path(self::BASE_PATH . "/{$name}Resource/Schemas/{$name}FormSchema.php");
        $namespace = "App\\Filament\\Resources\\{$name}Resource\\Schemas";
        $className = "{$name}FormSchema";

        $fieldsCode = '// TODO: Define form fields';

        if ($this->option('generate') && $modelClass) {
            $columns = $this->getModelColumns($modelClass);
            if (!empty($columns)) {
                $fieldsCode = collect($columns)->map(function ($column) {
                    $label = Str::of($column)->replace('_id', '')->replace('_', ' ')->title();

                    if (Str::endsWith($column, '_id')) {
                        $relation = Str::beforeLast($column, '_id');
                        $labelColumn = 'id'; // Default to 'id' as requested

                        return <<<PHP
                        Forms\\Components\\Select::make('{$column}')
                            ->label(__('{$label}'))
                            ->relationship('{$relation}', '{$labelColumn}')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->required()
                    PHP;
                    }

                    return <<<PHP
                    Forms\\Components\\TextInput::make('{$column}')
                        ->label(__('{$label}'))
                        ->required()
                PHP;
                })->implode(",\n\n            ");
            }
        }

        $content = $this->renderStub('form-schema.stub', [
            'className' => $className,
            'namespace' => $namespace,
            'formFields' => $fieldsCode,
        ]);

        $this->writeFile($path, $content);
    }

    protected function generateTableSchema(string $name, ?string $modelClass = null): void
    {
        $path = base_path(self::BASE_PATH . "/{$name}Resource/Schemas/{$name}TableSchema.php");
        $namespace = "App\\Filament\\Resources\\{$name}Resource\\Schemas";
        $className = "{$name}TableSchema";

        $columnsCode = '// TODO: Define table columns';

        if ($this->option('generate') && $modelClass) {
            $columns = $this->getModelColumns($modelClass);
            if (!empty($columns)) {
                $columnsCode = collect($columns)->map(function ($column) {
                    return "Tables\\Columns\\TextColumn::make('{$column}')"
                        . "->label(__('" . Str::headline($column) . "'))";
                })->implode(",\n            ");
            }
        }

        $content = $this->renderStub('table-schema.stub', [
            'className' => $className,
            'namespace' => $namespace,
            'tableColumns' => $columnsCode,
        ]);

        $this->writeFile($path, $content);
    }

    protected function generateFilamentResource(string $name, string $modelName): void
    {
        $resourcePath = base_path(self::BASE_PATH . "/{$name}Resource.php");

        $content = $this->renderStub('resource.stub', [
            'resourceName' => $name,
            'modelName' => $modelName,
            'formSchemaClass' => "App\\Filament\\Resources\\{$name}Resource\\Schemas\\{$name}FormSchema",
            'tableSchemaClass' => "App\\Filament\\Resources\\{$name}Resource\\Schemas\\{$name}TableSchema",
            'resourceNamespace' => 'App\\Filament\\Resources',
            'modelNamespace' => "App\\Models\\{$modelName}",
            'singularLabel' => Str::headline($name),
        ]);

        $this->writeFile($resourcePath, $content);
        $this->generateResourcePages($name, $modelName);
    }

    protected function generateResourcePages(string $name, string $modelName): void
    {
        $pagesPath = base_path(self::BASE_PATH . "/{$name}Resource/Pages");
        $this->files->ensureDirectoryExists($pagesPath);

        $pages = [
            'list-page.stub' => "List{$name}.php",
            'create-page.stub' => "Create{$name}.php",
            'edit-page.stub' => "Edit{$name}.php",
        ];

        foreach ($pages as $stub => $filename) {
            $content = $this->renderStub($stub, [
                'resourceName' => $name,
                'modelName' => $modelName,
            ]);

            $this->writeFile("{$pagesPath}/{$filename}", $content);
        }
    }

    protected function renderStub(string $stubFile, array $replacements): string
    {
        $stubPath = base_path("stubs/filament-resource/{$stubFile}");

        if (! $this->files->exists($stubPath)) {
            $this->error("❌ Stub not found: {$stubPath}");
            return '';
        }

        $stub = $this->files->get($stubPath);

        foreach ($replacements as $key => $value) {
            $stub = str_replace('{{' . $key . '}}', $value, $stub);
        }

        return $stub;
    }

    protected function writeFile(string $path, string $content): void
    {
        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);
    }
}
