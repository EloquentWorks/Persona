<?php

namespace EloquentWorks\Persona\Support;

use EloquentWorks\Persona\Models\Persona;

/**
 * Class ProfileCompleteness
 *
 * This class provides methods to assess the completeness of a Persona's profile.
 * It checks for the presence of required fields and calculates a completeness score.
 */
final class ProfileCompleteness
{
    /**
     * @return array<string, bool>
     */
    public static function checklist(Persona $persona): array
    {
        // Check if the persona has filled out the required fields for profile completeness
        return [
            'display_name' => filled($persona->getAttribute('display_name')),
            'headline' => filled($persona->getAttribute('headline')),
            'bio' => filled($persona->getAttribute('bio')),
            'location' => filled($persona->getAttribute('location')),
            'website_url' => filled($persona->getAttribute('website_url')),
            'avatar_path' => filled($persona->getAttribute('avatar_path')),
            'banner_path' => filled($persona->getAttribute('banner_path')),
            'social_links' => self::hasArrayValue($persona->getAttribute('social_links')),
            'custom_links' => self::hasArrayValue($persona->getAttribute('custom_links')),
        ];
    }

    /**
     * Calculate the profile completeness score as a percentage.
     *
     * @param  Persona  $persona  The persona instance to calculate the score for.
     * @return int The profile completeness score as a percentage (0-100).
     */
    public static function score(Persona $persona): int
    {
        // Get the checklist of completed fields for the persona
        $checklist = self::checklist($persona);

        // If the checklist is empty, return a score of 0
        if ($checklist === []) {
            return 0;
        }

        // Count the number of completed fields in the checklist
        $completed = collect($checklist)->filter()->count();

        // Calculate the completeness score as a percentage and round it to the nearest integer
        return (int) round(($completed / count($checklist)) * 100);
    }

    /**
     * Check if the given value is a non-empty array.
     *
     * @param  mixed  $value  The value to check.
     * @return bool True if the value is a non-empty array, false otherwise.
     */
    private static function hasArrayValue(mixed $value): bool
    {
        // Check if the value is an array and has at least one non-empty element
        return is_array($value) && count(array_filter($value)) > 0;
    }
}
