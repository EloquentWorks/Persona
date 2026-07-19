<?php

namespace EloquentWorks\Persona\Traits;

use EloquentWorks\Persona\Models\AdvancedPersona;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Optional user-model trait for applications that want the enhanced Persona model
 * without replacing the package's original HasPersona trait.
 */
trait HasAdvancedPersona
{
    use HasPersona;

    /**
     * Get the advanced persona associated with the user.
     *
     * @return HasOne<AdvancedPersona, $this>
     */
    public function advancedPersona(): HasOne
    {
        /** @var HasOne<AdvancedPersona, $this> $relationship */
        $relationship = $this->hasOne(AdvancedPersona::class, 'user_id');

        // Ensure that the advanced persona is created automatically when the user is created
        return $relationship;
    }
}
