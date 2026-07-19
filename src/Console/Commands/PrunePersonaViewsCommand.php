<?php

namespace EloquentWorks\Persona\Console\Commands;

use EloquentWorks\Persona\Models\PersonaView;
use Illuminate\Console\Command;

/**
 * @internal
 */
final class PrunePersonaViewsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'persona:prune-views
        {--days= : Delete views older than this many days. Defaults to persona.views.retention_days.}
        {--dry-run : Show how many rows would be deleted without deleting them.}
        {--force : Required to delete rows.}';

    /**
     * @var string
     */
    protected $description = 'Prune old Persona profile view records.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Get the number of days from the --days option or the configuration, defaulting to 365 if not set.
        $days = (int) ($this->option('days') ?: config('persona.views.retention_days', 365));

        // Validate that the number of days is greater than zero.
        if ($days <= 0) {
            // Print an error message to the console if the number of days is not greater than zero.
            $this->components->error('The --days option must be greater than zero.');

            // Return a failure status code to indicate that the command did not complete successfully.
            return self::FAILURE;
        }

        // Build a query to select Persona view records that are older than the specified number of days.
        $query = PersonaView::query()->where('viewed_at', '<', now()->subDays($days));
        $count = (clone $query)->count();

        // If there are no records to prune, print a message and return success.
        if ($this->option('dry-run')) {
            // Print the number of records that would be pruned without actually deleting them.
            $this->components->info("{$count} Persona view records would be pruned.");

            // Return a success status code to indicate that the command completed successfully.
            return self::SUCCESS;
        }

        // If there are no records to prune, print a message and return success.
        if (! $this->option('force')) {
            // Print a warning message to the console indicating that no records were deleted and that the --force option is required to prune old views.
            $this->components->warn('No records were deleted. Re-run with --force to prune old views.');

            // Return a success status code to indicate that the command completed successfully.
            return self::SUCCESS;
        }

        // Delete the records that match the query and print a success message with the number of records deleted.
        $query->delete();

        // Print a success message to the console indicating how many Persona view records were pruned.
        $this->components->success("Pruned {$count} Persona view records.");

        // Return a success status code to indicate that the command completed successfully.
        return self::SUCCESS;
    }
}
