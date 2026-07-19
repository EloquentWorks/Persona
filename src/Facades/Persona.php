<?php

namespace EloquentWorks\Persona\Facades;

use EloquentWorks\Persona\Models\Persona as PersonaModel;
use EloquentWorks\Persona\Support\PersonaManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isReservedUsername(string $username)
 * @method static int completenessScore(PersonaModel $persona)
 * @method static PersonaModel|null resolveHistoricalSlug(string $slug)
 * @method static PersonaModel updateProfile(Model $user, array<string, mixed> $attributes)
 *
 * @see PersonaManager
 */
final class Persona extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PersonaManager::class;
    }
}
