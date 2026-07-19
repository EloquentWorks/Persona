<?php

namespace EloquentWorks\Persona\Support;

use Illuminate\Support\Str;

/**
 * Class ReservedUsername
 *
 * This class provides functionality to check if a given username is reserved.
 * It normalizes the username and checks against a list of reserved usernames,
 * which can be configured in the application settings.
 */
final class ReservedUsername
{
    /**
     * Check if a given username is reserved.
     *
     * @param  string  $username  The username to check.
     * @return bool True if the username is reserved, false otherwise.
     */
    public static function isReserved(string $username): bool
    {
        // Normalize the username by converting it to lowercase and slugifying it.
        $username = Str::lower(Str::slug($username));

        // Check if the normalized username is in the list of reserved usernames.
        return in_array($username, self::all(), true);
    }

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        // Get the reserved usernames from the configuration, falling back to defaults if not set.
        $configured = config('persona.usernames.reserved', []);

        // If the configured value is not an array, return the default reserved usernames.
        if (! is_array($configured)) {
            return self::defaults();
        }

        // Merge the configured reserved usernames with the default reserved usernames, ensuring uniqueness and lowercasing.
        return array_values(array_unique(array_map(
            static fn (mixed $name): string => Str::lower(Str::slug((string) $name)),
            array_merge(self::defaults(), $configured)
        )));
    }

    /**
     * @return list<string>
     */
    public static function defaults(): array
    {
        // Default reserved usernames that should not be allowed for personas.
        return [
            'admin',
            'administrator',
            'api',
            'app',
            'billing',
            'dashboard',
            'help',
            'login',
            'logout',
            'me',
            'register',
            'root',
            'security',
            'settings',
            'staff',
            'support',
            'system',
        ];
    }
}
