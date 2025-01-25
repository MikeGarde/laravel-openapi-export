<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Orchestra\Testbench\Concerns\WithWorkbench;

class TestCase extends BaseTestCase
{
    use WithWorkbench;

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \MikeGarde\OpenApiExport\OpenApiServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [];
    }
}
