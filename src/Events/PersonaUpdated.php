<?php

namespace EloquentWorks\Persona\Events;

use EloquentWorks\Persona\Models\Persona;

/**
 * Event fired when a Persona profile is updated.
 */
class PersonaUpdated
{
    /**
     * Create a new event instance.
     *
     * @param  Persona  $persona  The Persona profile related to the event.
     * @return void Returns nothing.
     */
    public function __construct(public Persona $persona)
    {
        //
    }
}
