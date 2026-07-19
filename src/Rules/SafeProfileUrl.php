<?php

namespace EloquentWorks\Persona\Rules;

use Closure;
use EloquentWorks\Persona\Support\SafeProfileUrl as SafeProfileUrlSupport;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * A validation rule that checks if a given URL is a safe profile URL.
 */
final class SafeProfileUrl implements ValidationRule
{
    /**
     * Validate the given attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): void  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Validate the value using the SafeProfileUrlSupport class
        if (! SafeProfileUrlSupport::isSafe($value === null ? null : (string) $value)) {
            $fail('The :attribute must be a safe http or https URL.');
        }
    }
}
