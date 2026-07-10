<?php

namespace EloquentWorks\Persona\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class InstallPersonaCommand
 *
 * This command is responsible for installing the Persona package by publishing its configuration,
 * migrations, and optionally views and route snippets. It provides options to force overwrite
 * existing files and to include additional assets.
 */
class InstallPersonaCommand extends Command
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'persona:install
        {--force : Overwrite any existing published files}
        {--views : Publish Persona views}
        {--routes : Publish a copyable Persona route snippet}';

    /** @var string The console command description. */
    protected $description = 'Install the Persona package by publishing config, migrations, and optional assets.';

    /**
     * Execute the console command.
     *
     * @return int Returns the exit status code.
     */
    public function handle(): int
    {
        // Determine if the --force option was provided to overwrite existing files.
        $force = (bool) $this->option('force');

        // Display an informational message indicating the start of the installation process.
        $this->components->info('Installing Persona...');

        // Publish the Persona configuration file, migrations, and optionally views and routes based on the provided options.
        $this->callSilent('vendor:publish', [
            '--tag' => 'persona-config',
            '--force' => $force,
        ]);

        // Publish the Persona migration files to the application's database/migrations directory.
        $this->callSilent('vendor:publish', [
            '--tag' => 'persona-migrations',
            '--force' => $force,
        ]);

        // Conditionally publish the Persona views if the --views option was provided.
        if ((bool) $this->option('views')) {
            $this->callSilent('vendor:publish', [
                '--tag' => 'persona-views',
                '--force' => $force,
            ]);
        }

        // Conditionally publish the Persona route snippet if the --routes option was provided.
        if ((bool) $this->option('routes')) {
            $this->callSilent('vendor:publish', [
                '--tag' => 'persona-routes',
                '--force' => $force,
            ]);
        }

        // Display instructions for adding the Persona route snippet to the application's routes/web.php file.
        $this->newLine();
        $this->components->info('Add this to routes/web.php when you want public profile routes:');
        $this->line('use Illuminate\Support\Facades\Route;');
        $this->newLine();
        $this->line('Route::persona();');
        $this->newLine();

        // Display a success message indicating that the Persona package has been installed successfully.
        $this->components->success('Persona installed successfully.');

        // Return a success exit code to indicate that the command executed successfully.
        return self::SUCCESS;
    }
}
