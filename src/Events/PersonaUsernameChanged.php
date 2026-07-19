<?php

namespace EloquentWorks\Persona\Events;

use EloquentWorks\Persona\Models\Persona;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Event that is fired when a persona's username (slug) is changed.
 */
final class PersonaUsernameChanged
{
    /**
     * Create a new event instance.
     *
     * @param Persona $persona The persona whose username was changed.
     * @param string $oldSlug The old username (slug).
     * @param string $newSlug The new username (slug).
     * @param Authenticatable|null $changedBy The user who changed the username, if applicable.
     */
    public function __construct(
        public Persona $persona,
        public string $oldSlug,
        public string $newSlug,
        public ?Authenticatable $changedBy = null,
    ) {
    }
}
