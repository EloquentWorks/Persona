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
        parent::getEnvironmentSetUp($app);

        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

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
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

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
            $table->unsignedBigInteger('profile_views')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }
}
