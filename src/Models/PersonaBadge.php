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
 * @property string $name
 * @property string|null $label
 * @property string|null $description
 * @property string|null $icon
 * @property int $sort_order
 * @property bool $is_public
 * @property string|null $awarded_by_type
 * @property int|null $awarded_by_id
 * @property array<string, mixed>|null $metadata
 * @property Carbon|null $awarded_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $revoked_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PersonaBadge extends Model
{
    /**
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
        'metadata' => 'array',
        'awarded_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        return (string) config('persona.tables.badges', 'persona_badges');
    }

    /**
     * Get the persona associated with this badge.
     *
     * @return BelongsTo<Persona, $this>
     */
    public function persona(): BelongsTo
    {
        /** @var class-string<Persona> $personaModel */
        $personaModel = config('persona.models.persona', Persona::class);

        /** @var BelongsTo<Persona, $this> $relationship */
        $relationship = $this->belongsTo($personaModel, 'persona_id');

        // Ensure that the relationship is eager loaded with the default persona model.
        return $relationship;
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function awardedBy(): MorphTo
    {
        // The awardedBy method defines a polymorphic relationship to the model that awarded the badge. This allows for flexibility in associating badges with different types of models (e.g., User, Admin, etc.) that can award badges.
        return $this->morphTo('awarded_by');
    }

    /**
     * Determine if the badge is currently active (not revoked and not expired).
     */
    public function isActive(): bool
    {
        // The isActive method checks if the badge is currently active by verifying that it has not been revoked and that it has not expired. If the badge has a revoked_at timestamp, it is considered inactive. If the expires_at timestamp is null or in the future, the badge is considered active.
        if ($this->revoked_at !== null) {
            return false;
        }

        // Check if the badge has an expiration date and if it is in the future. If there is no expiration date, the badge is considered active.
        return $this->expires_at === null || $this->expires_at->isFuture();
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePublic(Builder $query): Builder
    {
        // The scopePublic method defines a query scope that filters badges to only include those that are marked as public. This allows for easy retrieval of public badges in queries.
        return $query->where('is_public', true);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeActive(Builder $query): Builder
    {
        // The scopeActive method defines a query scope that filters badges to only include those that are currently active. It checks that the revoked_at timestamp is null (indicating the badge has not been revoked) and that the expires_at timestamp is either null (indicating no expiration) or in the future (indicating the badge has not yet expired).
        return $query
            ->whereNull('revoked_at')
            ->where(function (Builder $query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeNamed(Builder $query, string $name): Builder
    {
        // The scopeNamed method defines a query scope that filters badges to only include those with a specific name. This allows for easy retrieval of badges by their name in queries.
        return $query->where('name', $name);
    }
}
