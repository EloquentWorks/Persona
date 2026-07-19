<?php

namespace EloquentWorks\Persona\Rules;

use Closure;
use EloquentWorks\Persona\Support\ReservedUsername;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Validation rule to check if a username is reserved.
 */
final class ReservedPersonaUsername implements ValidationRule
{
    /**
     * Validate the attribute.
     *
     * @param  Closure(string, string|null=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the username is reserved using the ReservedUsername class.
        if (ReservedUsername::isReserved((string) $value)) {
            $fail('The selected :attribute is reserved.');
        }
    }
}
