<?php

namespace Justnotiyann\FilamentSchemaResource;

use Justnotiyann\FilamentSchemaResource\Console\MakeFilamentSchemaResourceCommand;
use Illuminate\Support\ServiceProvider;

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
