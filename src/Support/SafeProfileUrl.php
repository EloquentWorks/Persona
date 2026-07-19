<?php

namespace EloquentWorks\Persona\Support;

final class SafeProfileUrl
{
    /**
     * Determine if the given URL is safe to use in a profile.
     */
    public static function isSafe(?string $url): bool
    {
        // If the URL is null or empty, we consider it safe.
        if ($url === null || $url === '') {
            return true;
        }

        // If the URL is not a string, we consider it unsafe.
        $parts = parse_url($url);

        // If parse_url fails, it returns false. We consider such URLs unsafe.
        if (! is_array($parts)) {
            return false;
        }

        // Extract the scheme from the parsed URL and convert it to lowercase for comparison.
        $scheme = isset($parts['scheme']) ? strtolower((string) $parts['scheme']) : null;

        // If the scheme is null, we consider the URL unsafe.
        if ($scheme === null) {
            return false;
        }

        // Get the allowed schemes from the configuration, defaulting to ['http', 'https'] if not set.
        $allowed = config('persona.links.allowed_schemes', ['http', 'https']);

        // If the allowed schemes are not an array, we default to ['http', 'https'].
        if (! is_array($allowed)) {
            $allowed = ['http', 'https'];
        }

        // Check if the scheme of the URL is in the list of allowed schemes.
        return in_array($scheme, $allowed, true);
    }
}
