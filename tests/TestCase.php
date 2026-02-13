<?php

namespace LBCDev\FilamentMapsFields\Tests;

use Filament\FilamentServiceProvider;
use LBCDev\FilamentMapsFields\FilamentMapsFieldsServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup database
        $this->setUpDatabase();
    }

    /**
     * Get package providers.
     */
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            FilamentMapsFieldsServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Setup view paths
        $app['config']->set('view.paths', [
            __DIR__ . '/../resources/views',
            resource_path('views'),
        ]);
    }

    /**
     * Setup the test database.
     */
    protected function setUpDatabase(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Fixtures/database/migrations');
    }
}
