<?php

namespace Tests;

use EloquentWorks\Persona\PersonaServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param  mixed  $app  The application instance.
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        // Return an array of service provider classes to be registered for the test environment.
        return [
            PersonaServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  mixed  $app  The application instance.
     * @return void Returns nothing.
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Call the parent method to ensure any necessary setup is performed.
        parent::getEnvironmentSetUp($app);

        // Set the application key to a base64-encoded string of 32 'a' characters for testing purposes.
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));

        // Configure the database connection to use an in-memory SQLite database for testing.
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set the authentication user model to a custom User class defined in the Fixtures namespace for testing.
        $app['config']->set('auth.providers.users.model', Fixtures\User::class);
        $app['config']->set('persona.models.user', Fixtures\User::class);
    }

    /**
     * Define database migrations for tests.
     *
     * @return void Returns nothing.
     */
    protected function defineDatabaseMigrations(): void
    {
        // Create the 'users' table with necessary columns for testing.
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        // Load the package's database migrations from the specified directory for testing.
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
