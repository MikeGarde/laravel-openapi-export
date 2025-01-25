<?php

namespace MikeGarde\OpenApiExport;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class OpenApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(OpenApiGenerator::class, function ($app) {
            return new OpenApiGenerator();
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\GenerateOpenApiCommand::class,
            ]);
        }
    }
}
