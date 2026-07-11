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

        // Create the 'persona_profiles' table with necessary columns for testing.
        Schema::create('persona_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('display_name')->nullable();
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('website_url')->nullable();
            $table->string('avatar_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->json('social_links')->nullable();
            $table->json('custom_links')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_public')->default(true);
            $table->unsignedInteger('username_tokens')->default(0);
            $table->timestamp('username_tokens_granted_at')->nullable();
            $table->timestamp('username_changed_at')->nullable();
            $table->unsignedBigInteger('profile_views')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }
}
