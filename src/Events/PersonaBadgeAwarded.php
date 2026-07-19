<?php

namespace EloquentWorks\Persona\Events;

use EloquentWorks\Persona\Models\Persona;
use EloquentWorks\Persona\Models\PersonaBadge;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Event that is fired when a badge is awarded to a persona.
 */
final class PersonaBadgeAwarded
{
    /**
     * Create a new event instance.
     *
     * @param  Persona  $persona  The persona that received the badge.
     * @param  PersonaBadge  $badge  The badge that was awarded.
     * @param  Authenticatable|null  $awardedBy  The user who awarded the badge, if any.
     */
    public function __construct(
        public Persona $persona,
        public PersonaBadge $badge,
        public ?Authenticatable $awardedBy = null,
    ) {}
}
