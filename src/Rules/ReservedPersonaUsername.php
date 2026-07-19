<?php

namespace EloquentWorks\Persona\Rules;

use Closure;
use EloquentWorks\Persona\Support\ReservedUsername;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * A validation rule that checks if a given username is reserved.
 */
final class ReservedPersonaUsername implements ValidationRule
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
        // Check if the username is reserved using the ReservedUsername class
        if (ReservedUsername::isReserved((string) $value)) {
            $fail('The selected :attribute is reserved.');
        }
    }
}
