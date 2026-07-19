<?php

namespace EloquentWorks\Persona\Support;

use EloquentWorks\Persona\Models\Persona;
use EloquentWorks\Persona\Models\PersonaUsernameHistory;
use Illuminate\Database\Eloquent\Model;

/**
 * PersonaManager is a service class that provides methods for managing personas.
 */
final class PersonaManager
{
    /**
     * Generate a slug from a given name.
     */
    public function isReservedUsername(string $username): bool
    {
        // Check if the username is reserved using the ReservedUsername class
        return ReservedUsername::isReserved($username);
    }

    /**
     * Get the completeness score of a persona.
     */
    public function completenessScore(Persona $persona): int
    {
        // Calculate the completeness score of a persona using the ProfileCompleteness class
        return ProfileCompleteness::score($persona);
    }

    /**
     * Resolve an old username to the latest visible profile when username history is enabled.
     */
    public function resolveHistoricalSlug(string $slug): ?Persona
    {
        // If username history is not enabled, return null
        $history = PersonaUsernameHistory::query()
            ->forOldSlug($slug)
            ->latest()
            ->first();

        // If no history is found, return null
        if (! $history) {
            return null;
        }

        // Get the associated persona from the history
        $persona = $history->persona()->first();

        // Return the persona if it exists and is visible, otherwise return null
        return $persona instanceof Persona && $persona->isVisible() ? $persona : null;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateProfile(Model $user, array $attributes): Persona
    {
        // Ensure the user model has the updatePersona method, which indicates it uses the HasPersona trait
        if (! method_exists($user, 'updatePersona')) {
            throw new \InvalidArgumentException('The model must use the HasPersona trait.');
        }

        /** @var Persona $persona */
        $persona = $user->updatePersona($attributes);

        // If the slug has changed, record the old slug in the username history
        return $persona;
    }
}
