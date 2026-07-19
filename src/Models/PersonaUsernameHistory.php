<?php

namespace EloquentWorks\Persona\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $persona_id
 * @property string $old_slug
 * @property string $new_slug
 * @property string|null $changed_by_type
 * @property int|null $changed_by_id
 * @property string|null $reason
 * @property array<string, mixed>|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PersonaUsernameHistory extends Model
{
    /**
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        return (string) config('persona.tables.username_histories', 'persona_username_histories');
    }

    /**
     * Get the persona associated with this username history.
     *
     * @return BelongsTo<Persona, $this>
     */
    public function persona(): BelongsTo
    {
        /** @var class-string<Persona> $personaModel */
        $personaModel = config('persona.models.persona', Persona::class);

        /** @var BelongsTo<Persona, $this> $relationship */
        $relationship = $this->belongsTo($personaModel, 'persona_id');

        // Ensure the relationship uses the correct table name for the Persona model.
        return $relationship;
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function changedBy(): MorphTo
    {
        // This defines a polymorphic relationship to the model that changed the username.
        return $this->morphTo('changed_by');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForOldSlug(Builder $query, string $slug): Builder
    {
        // This scope filters the query to find records with a specific old slug.
        return $query->where('old_slug', $slug);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForNewSlug(Builder $query, string $slug): Builder
    {
        // This scope filters the query to find records with a specific new slug.
        return $query->where('new_slug', $slug);
    }
}
