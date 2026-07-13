<?php

namespace EloquentWorks\Persona\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use LogicException;

/**
 * @property int $id
 * @property int $persona_id
 * @property int|null $parent_id
 * @property int $user_id
 * @property string $body
 * @property bool $is_approved
 * @property bool $is_pinned
 * @property Carbon|null $edited_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read PersonaComment|null $parent
 * @property-read Collection<int, PersonaComment> $replies
 *
 * @method static Builder<static> approved()
 * @method static Builder<static> pinned()
 * @method static Builder<static> topLevel()
 * @method static Builder<static> repliesOnly()
 */
class PersonaComment extends Model
{
    use SoftDeletes;

    /** @var list<string> The attributes that are mass assignable. */
    protected $fillable = [
        'persona_id',
        'parent_id',
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
     * Boot the model and define event listeners for deleting comments and their replies.
     *
     * @return void Returns nothing.
     */
    protected static function booted(): void
    {
        // When a comment is being deleted, this event listener will handle the deletion of its replies.
        static::deleting(function (PersonaComment $comment): void {
            if ($comment->isForceDeleting()) {
                // If the comment is being force deleted, also force delete all its replies, including those that are soft deleted.
                $comment->replies()
                    ->withTrashed()
                    ->get()
                    ->each(
                        fn (PersonaComment $reply): bool => $reply->forceDelete()
                    );

                return;
            }

            // If the comment is being soft deleted, also soft delete all its replies.
            $comment->replies()
                ->get()
                ->each(
                    fn (PersonaComment $reply): bool => $reply->delete()
                );
        });
    }

    /**
     * Get the parent comment.
     *
     * @return BelongsTo<PersonaComment, $this>
     */
    public function parent(): BelongsTo
    {
        /** @var class-string<PersonaComment> $commentModel */
        $commentModel = config(
            'persona.models.comment',
            PersonaComment::class
        );

        /** @var BelongsTo<PersonaComment, $this> $relationship */
        $relationship = $this->belongsTo(
            $commentModel,
            'parent_id'
        );

        // Return the relationship to the parent comment, allowing access to the parent comment for this comment.
        return $relationship;
    }

    /**
     * Get the direct replies to this comment.
     *
     * @return HasMany<PersonaComment, $this>
     */
    public function replies(): HasMany
    {
        /** @var class-string<PersonaComment> $commentModel */
        $commentModel = config(
            'persona.models.comment',
            PersonaComment::class
        );

        /** @var HasMany<PersonaComment, $this> $relationship */
        $relationship = $this->hasMany(
            $commentModel,
            'parent_id'
        );

        // Return the relationship to the direct replies, allowing access to the replies for this comment.
        return $relationship;
    }

    /**
     * Scope a query to top-level comments.
     *
     * @param  Builder<static>  $query  The query Builder instance.
     * @return Builder<static> Returns the modified query builder instance with the top-level scope applied.
     */
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to replies.
     *
     * @param  Builder<static>  $query  The query builder instance.
     * @return Builder<static> Returns the modified query builder instance with the replies scope applied.
     */
    public function scopeRepliesOnly(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id');
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
     * Scope a query to only include pinned comments.
     *
     * @param  Builder<static>  $query  The query builder instance.
     * @return Builder<static> Returns the modified query builder instance with the pinned scope applied.
     */
    public function scopePinned(Builder $query): Builder
    {
        // Filter the query to only include comments where 'is_pinned' is true.
        return $query->where('is_pinned', true);
    }

    /**
     * Determine whether this comment is a reply to another comment.
     *
     * @return bool Returns true if this comment is a reply, false otherwise.
     */
    public function isReply(): bool
    {
        // A comment is considered a reply if it has a parent comment (i.e., 'parent_id' is not null).
        return $this->parent_id !== null;
    }

    /**
     * Determine whether this is a top-level comment.
     *
     * @return bool Returns true if this comment is a top-level comment, false otherwise.
     */
    public function isTopLevel(): bool
    {
        // A top-level comment is defined as one that does not have a parent comment (i.e., 'parent_id' is null).
        return $this->parent_id === null;
    }

    /**
     * Add a direct reply to this comment.
     *
     * @param  Model  $user  The user model instance representing the author of the reply.
     * @param  string  $body  The body of the reply.
     * @return PersonaComment Returns the newly created reply comment instance.
     *
     * @throws InvalidArgumentException If the reply body is empty or exceeds the maximum length.
     * @throws LogicException If comments or replies are disabled, or if this comment is already a reply.
     */
    public function addReply(Model $user, string $body): PersonaComment
    {
        // Validate that comments and replies are enabled in the configuration before proceeding.
        if (! config('persona.comments.enabled', true)) {
            throw new LogicException('Persona comments are disabled.');
        }

        // Validate that replies are enabled in the configuration before proceeding.
        if (! config('persona.comments.replies_enabled', true)) {
            throw new LogicException('Persona comment replies are disabled.');
        }

        // Validate that this comment is not already a reply, as nested replies are not allowed.
        if ($this->isReply()) {
            throw new LogicException(
                'Replies cannot contain nested replies.'
            );
        }

        // Trim whitespace from the reply body to ensure accurate validation.
        $body = trim($body);

        // Validate that the reply body is not empty.
        if ($body === '') {
            throw new InvalidArgumentException(
                'Reply body cannot be empty.'
            );
        }

        $maxLength = (int) config(
            'persona.comments.max_length',
            1000
        );

        // Validate that the reply body does not exceed the maximum allowed length as defined in the configuration.
        if (mb_strlen($body) > $maxLength) {
            throw new InvalidArgumentException(
                "Reply body may not be greater than {$maxLength} characters."
            );
        }

        /** @var PersonaComment $reply */
        $reply = $this->replies()->create([
            'persona_id' => $this->persona_id,
            'user_id' => $user->getKey(),
            'body' => $body,
            'is_approved' => ! config(
                'persona.comments.require_approval',
                false
            ),
        ]);

        // Return the newly created reply comment instance.
        return $reply;
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
     * Edit the comment body.
     *
     * @param  string  $body  The new body of the comment.
     * @return bool Returns true if the comment was successfully edited and saved, false otherwise.
     *
     * @throws InvalidArgumentException
     */
    public function edit(string $body): bool
    {
        // Trim whitespace from the comment body to ensure accurate validation.
        $body = trim($body);

        // Validate that the comment body is not empty.
        if ($body === '') {
            throw new InvalidArgumentException(
                'Comment body cannot be empty.'
            );
        }

        // Validate that the comment body does not exceed the maximum allowed length as defined in the configuration.
        $maxLength = (int) config(
            'persona.comments.max_length',
            1000
        );

        //  Validate that the comment body does not exceed the maximum allowed length as defined in the configuration.
        if (mb_strlen($body) > $maxLength) {
            throw new InvalidArgumentException(
                "Comment body may not be greater than {$maxLength} characters."
            );
        }

        // Update the comment body and set the edited_at timestamp to the current time, then save the model.
        return $this->forceFill([
            'body' => $body,
            'edited_at' => now(),
        ])->save();
    }
}
