<?php

namespace EloquentWorks\Persona\Models;

use EloquentWorks\Persona\Traits\InteractsWithPersonaEnhancements;

/**
 * Optional enhanced Persona model.
 *
 * Existing applications may keep using Persona::class. New applications that want
 * the v1.1 convenience methods may set config('persona.models.persona') to this class.
 */
class AdvancedPersona extends Persona
{
    /**
     * Enable the convenience methods for persona enhancements.
     */
    use InteractsWithPersonaEnhancements;
}
