<?php

namespace EloquentWorks\Persona\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $persona_id
 * @property int|null $viewer_id
 * @property string|null $session_id
 * @property string|null $ip_hash
 * @property string|null $user_agent_hash
 * @property string|null $referer
 * @property string|null $source
 * @property array<string, mixed>|null $metadata
 * @property Carbon $viewed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PersonaView extends Model
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
        'viewed_at' => 'datetime',
    ];

    /**
     * Get the table name for the PersonaView model.
     *
     * @return string The table name for the PersonaView model.
     */
    public function getTable(): string
    {
        // Return the table name for the PersonaView model, using the configured value or a default.
        return (string) config('persona.tables.views', 'persona_views');
    }

    /**
     * Get the persona associated with this view.
     *
     * @return BelongsTo<Persona, $this>
     */
    public function persona(): BelongsTo
    {
        /** @var class-string<Persona> $personaModel */
        $personaModel = config('persona.models.persona', Persona::class);

        /** @var BelongsTo<Persona, $this> $relationship */
        $relationship = $this->belongsTo($personaModel, 'persona_id');

        // @phpstan-ignore-next-line
        return $relationship;
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForPersona(Builder $query, Persona $persona): Builder
    {
        // Filter the query to only include views associated with the specified persona.
        return $query->where('persona_id', $persona->getKey());
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        // Filter the query to only include views that occurred within the specified number of days.
        return $query->where('viewed_at', '>=', now()->subDays($days));
    }
}
