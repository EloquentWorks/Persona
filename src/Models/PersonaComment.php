<?php

namespace EloquentWorks\Persona\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $persona_id
 * @property int $user_id
 * @property string $body
 * @property bool $is_approved
 * @property bool $is_pinned
 * @property Carbon|null $edited_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static Builder<static> approved()
 * @method static Builder<static> pinned()
 */
class PersonaComment extends Model
{
    use SoftDeletes;

    /** @var list<string> The attributes that are mass assignable. */
    protected $fillable = [
        'persona_id',
        'user_id',
        'body',
        'is_approved',
        'is_pinned',
        'edited_at',
    ];

    /** @var array<string, string> The attributes that should be cast to native types. */
    protected $casts = [
        'is_approved' => 'boolean',
        'is_pinned' => 'boolean',
        'edited_at' => 'datetime',
    ];

    /**
     * Get the table associated with the model.
     *
     * @return string Returns the name of the table associated with the model.
     */
    public function getTable(): string
    {
        // Determine the table name for the PersonaComment model from the configuration, falling back to 'persona_comments' if not set.
        return config('persona.tables.comments', 'persona_comments');
    }

    /**
     * Get the profile this comment belongs to.
     *
     * @return BelongsTo<Persona, $this>
     */
    public function persona(): BelongsTo
    {
        /** @var class-string<Persona> $personaModel */
        $personaModel = config('persona.models.persona', Persona::class);

        /** @var BelongsTo<Persona, $this> $relationship */
        $relationship = $this->belongsTo($personaModel, 'persona_id');

        // Return the relationship to the Persona model, allowing access to the associated profile for this comment.
        return $relationship;
    }

    /**
     * Get the user that made this comment.
     *
     * @return BelongsTo<Model, $this> Returns the relationship to the user model.
     */
    public function user(): BelongsTo
    {
        // Determine the user model class from the configuration, falling back to the default auth user model if not set.
        $userModel = config('persona.models.user') ?? config('auth.providers.users.model');

        // If no user model is configured, throw an exception to indicate that the user relationship cannot be established.
        return $this->belongsTo($userModel, 'user_id');
    }

    /**
     * Scope a query to only include approved comments.
     *
     * @param  Builder<static>  $query  The query builder instance.
     * @return Builder<static> Returns the modified query builder instance with the approved scope applied.
     */
    public function scopeApproved(Builder $query): Builder
    {
        // Filter the query to only include comments where 'is_approved' is true.
        return $query->where('is_approved', true);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePinned(Builder $query): Builder
    {
        // Filter the query to only include comments where 'is_pinned' is true.
        return $query->where('is_pinned', true);
    }

    /**
     * Approve the comment.
     *
     * @return bool Returns true if the comment was successfully approved and saved, false otherwise.
     */
    public function approve(): bool
    {
        // Approve the comment by setting 'is_approved' to true and saving the model.
        return $this->forceFill([
            'is_approved' => true,
        ])->save();
    }

    /**
     * Unapprove the comment.
     *
     * @return bool Returns true if the comment was successfully unapproved and saved, false otherwise.
     */
    public function unapprove(): bool
    {
        // Unapprove the comment by setting 'is_approved' to false and saving the model.
        return $this->forceFill([
            'is_approved' => false,
        ])->save();
    }

    /**
     * Pin the comment.
     *
     * @return bool Returns true if the comment was successfully pinned and saved, false otherwise.
     */
    public function pin(): bool
    {
        // Pin the comment by setting 'is_pinned' to true and saving the model.
        return $this->forceFill([
            'is_pinned' => true,
        ])->save();
    }

    /**
     * Unpin the comment.
     *
     * @return bool Returns true if the comment was successfully unpinned and saved, false otherwise.
     */
    public function unpin(): bool
    {
        // Unpin the comment by setting 'is_pinned' to false and saving the model.
        return $this->forceFill([
            'is_pinned' => false,
        ])->save();
    }

    /**
     * Edit the comment.
     *
     * @param  string  $body  The new body of the comment.
     * @return bool Returns true if the comment was successfully edited and saved, false otherwise.
     */
    public function edit(string $body): bool
    {
        // Edit the comment by updating the 'body' and 'edited_at' fields and saving the model.
        return $this->forceFill([
            'body' => $body,
            'edited_at' => now(),
        ])->save();
    }
}
