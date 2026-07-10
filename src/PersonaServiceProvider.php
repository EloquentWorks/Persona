<?php

namespace EloquentWorks\Persona;

use EloquentWorks\Persona\Console\Commands\InstallPersonaCommand;
use EloquentWorks\Persona\Http\Controllers\PersonaController;
use Illuminate\Routing\Router;
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

        // If we are not running in the console, skip the following console-specific bootstrapping.
        if (! $this->app->runningInConsole()) {
            return;
        }

        // Register the Persona installation command for use in the console.
        $this->commands([
            InstallPersonaCommand::class,
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
        /** @var Router $router */
        $router = $this->app['router'];

        // Check if the Route::persona() macro is already defined to avoid redefinition.
        if (Router::hasMacro('persona')) {
            return;
        }

        // Define the Route::persona() macro to register public profile routes with optional configuration.
        $router->macro('persona', function (array $options = []) use ($router): void {
            // Retrieve the Persona route configuration from the application's config, defaulting to an empty array if not set.
            $config = config('persona.routes', []);
            $config = is_array($config) ? $config : [];

            // Merge the provided options with the configuration values, falling back to defaults if necessary.
            $middleware = $options['middleware'] ?? $config['middleware'] ?? ['web'];
            $prefix = $options['prefix'] ?? $config['prefix'] ?? '';
            $path = $options['path'] ?? $config['path'] ?? '@{persona}';
            $name = $options['name'] ?? $config['name'] ?? 'persona.';
            $controller = $options['controller'] ?? $config['controller'] ?? PersonaController::class;

            // Ensure the middleware is either an array or a string; if not, default to ['web'].
            if (! is_array($middleware) && ! is_string($middleware)) {
                $middleware = ['web'];
            }

            // Ensure the controller is a valid class name; if not, default to PersonaController.
            if (! is_string($controller)) {
                $controller = PersonaController::class;
            }

            // Trim the prefix and path to remove any leading or trailing slashes, and construct the full URI for the route.
            $prefix = trim((string) $prefix, '/');
            $path = trim((string) $path, '/');
            $uri = trim($prefix.'/'.$path, '/');

            // Determine the route name for the 'show' action, ensuring it ends with '.show'.
            $routeName = str_ends_with((string) $name, '.show')
                ? (string) $name
                : Str::finish((string) $name, '.').'show';

            // Register the GET route for the public profile page, associating it with the specified controller and middleware.
            $router->get($uri, [$controller, 'show'])
                ->middleware($middleware)
                ->name($routeName);

            // Refresh the route name and action lookups to ensure the new route is recognized.
            $router->getRoutes()->refreshNameLookups();
            $router->getRoutes()->refreshActionLookups();
        });
    }
}
