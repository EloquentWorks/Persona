<?php

namespace EloquentWorks\Persona\Rules;

use Closure;
use EloquentWorks\Persona\Support\SafeProfileUrl as SafeProfileUrlSupport;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * A validation rule that checks if a given URL is safe for use as a profile URL.
 */
final class SafeProfileUrl implements ValidationRule
{
    /**
     * Validate the attribute.
     *
     * @param  Closure(string, string|null=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! SafeProfileUrlSupport::isSafe($value === null ? null : (string) $value)) {
            $fail('The :attribute must be a safe http or https URL.');
        }
    }
}
