<?php

namespace EloquentWorks\Persona;

use EloquentWorks\Persona\Console\Commands\InstallPersonaCommand;
use EloquentWorks\Persona\Console\Commands\PrunePersonaViewsCommand;
use EloquentWorks\Persona\Http\Controllers\PersonaController;
use EloquentWorks\Persona\Http\Middleware\EnsurePersonaIsVisible;
use EloquentWorks\Persona\Support\PersonaManager;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

/**
 * Service provider for the Persona package.
 */
class PersonaServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void Returns nothing.
     */
    public function register(): void
    {
        // Merge the package's configuration file with the application's configuration to allow customization.
        $this->mergeConfigFrom(__DIR__.'/../config/persona.php', 'persona');

        // Register the PersonaManager as a singleton in the service container for easy access throughout the application.
        $this->app->singleton(PersonaManager::class, fn (): PersonaManager => new PersonaManager);
        $this->app->alias(PersonaManager::class, 'persona');
    }

    /**
     * Bootstrap any package services.
     *
     * @return void Returns nothing.
     */
    public function boot(): void
    {
        // Register the Route::persona() macro to allow easy registration of public profile routes.
        $this->registerRoutesMacro();

        // Load the Persona views from the package's resources/views directory for use in the application.
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'persona');

        // Register the Persona middleware alias to ensure that the persona is visible before accessing certain routes.
        $this->app->make(Router::class)->aliasMiddleware('persona.visible', EnsurePersonaIsVisible::class);

        // Register a custom Blade directive to conditionally display content based on the visibility of a persona.
        Blade::if('personaVisible', static fn ($persona): bool => $persona?->isVisible() ?? false);

        // If we are not running in the console, skip the following console-specific bootstrapping.
        if (! $this->app->runningInConsole()) {
            return;
        }

        // Register the Persona installation command for use in the console.
        $this->commands([
            InstallPersonaCommand::class,
            PrunePersonaViewsCommand::class,
        ]);

        // Publish the Persona configuration file to the application's config directory for customization.
        $this->publishes([
            __DIR__.'/../config/persona.php' => config_path('persona.php'),
        ], 'persona-config');

        // Publish the Persona migrations to the application's database/migrations directory for customization.
        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'persona-migrations');

        // Publish the Persona views to the application's resources/views/vendor directory for customization.
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/persona'),
        ], 'persona-views');

        // Publish the Persona route snippet to the application's routes directory for easy inclusion.
        $this->publishes([
            __DIR__.'/../routes/web.php' => base_path('routes/persona.php'),
        ], 'persona-routes');
    }

    /**
     * Register the Route::persona() macro.
     *
     * @return void Returns nothing.
     */
    protected function registerRoutesMacro(): void
    {
        // Check if the Route::persona() macro is already registered to avoid duplicate registration.
        if (Router::hasMacro('persona')) {
            return;
        }

        // Define the Route::persona() macro to register public profile routes with customizable options.
        Router::macro('persona', function (array $options = []): void {
            /** @var Router $router */
            $router = $this;

            // Retrieve the default route configuration from the Persona package's configuration file.
            $config = config('persona.routes', []);
            $config = is_array($config) ? $config : [];

            // Merge the provided options with the default configuration, allowing for customization of middleware, prefix, path, name, and controller.
            $middleware = $options['middleware'] ?? $config['middleware'] ?? ['web'];
            $prefix = $options['prefix'] ?? $config['prefix'] ?? '';
            $path = $options['path'] ?? $config['path'] ?? '@{persona}';
            $name = $options['name'] ?? $config['name'] ?? 'persona.';
            $controller = $options['controller'] ?? $config['controller'] ?? PersonaController::class;

            // Ensure that the middleware is either an array or a string; if not, default to the 'web' middleware.
            if (! is_array($middleware) && ! is_string($middleware)) {
                $middleware = ['web'];
            }

            // Ensure that the controller is a valid class; if not, default to the PersonaController.
            if (! is_string($controller) || ! class_exists($controller)) {
                $controller = PersonaController::class;
            }

            // Trim leading and trailing slashes from the prefix and path, and concatenate them to form the full URI for the route.
            $prefix = trim((string) $prefix, '/');
            $path = trim((string) $path, '/');
            $uri = trim($prefix.'/'.$path, '/');

            // Determine the route name for the public profile route, ensuring it ends with '.show' for consistency.
            $routeName = str_ends_with((string) $name, '.show')
                ? (string) $name
                : Str::finish((string) $name, '.').'show';

            // Register the GET route for the public profile, associating it with the specified controller and middleware, and assigning the determined route name.
            $router->get($uri, [$controller, 'show'])
                ->middleware($middleware)
                ->name($routeName);

            // Refresh the route name and action lookups to ensure that the newly registered route is recognized by the router.
            $router->getRoutes()->refreshNameLookups();
            $router->getRoutes()->refreshActionLookups();
        });
    }
}
