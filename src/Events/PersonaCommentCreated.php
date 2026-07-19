<?php

namespace EloquentWorks\Persona\Events;

use EloquentWorks\Persona\Models\Persona;
use EloquentWorks\Persona\Models\PersonaComment;

/**
 * Event triggered when a comment is created on a persona.
 */
final class PersonaCommentCreated
{
    /**
     * Create a new event instance.
     *
     * @param Persona $persona The persona on which the comment was created.
     * @param PersonaComment $comment The comment that was created.
     */
    public function __construct(
        public Persona $persona,
        public PersonaComment $comment,
    ) {
    }
}
