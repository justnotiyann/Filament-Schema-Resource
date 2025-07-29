<?php

namespace Iyan\FilamentSchemaResource;

use Illuminate\Support\ServiceProvider;
use Iyan\FilamentSchemaResource\Console\MakeFilamentSchemaResourceCommand;

class FilamentSchemaResourceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeFilamentSchemaResourceCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../stubs' => base_path('stubs'),
            ], 'filament-schema-stubs');
        }
    }
}
