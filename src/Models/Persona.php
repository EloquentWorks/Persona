<?php

namespace EloquentWorks\Persona\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;

/**
 * This model is used to manage user profiles, including their display name, bio, social links, and other related information.
 *
 * @property int $id
 * @property int $user_id
 * @property string $slug
 * @property string|null $display_name
 * @property string|null $headline
 * @property string|null $bio
 * @property string|null $location
 * @property string|null $website_url
 * @property string|null $avatar_path
 * @property string|null $banner_path
 * @property array<string, mixed>|null $social_links
 * @property array<string, mixed>|null $custom_links
 * @property array<string, mixed>|null $metadata
 * @property-read Collection<int, PersonaComment> $comments
 * @property bool $is_public
 * @property int $profile_views
 * @property int $username_tokens
 * @property Carbon|null $username_tokens_granted_at
 * @property Carbon|null $username_changed_at
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static> public()
 * @method static Builder<static> published()
 * @method static Builder<static> visible()
 */
class Persona extends Model
{
    /** @var list<string> The attributes that are mass assignable. */
    protected $fillable = [
        'user_id',
        'slug',
        'display_name',
        'headline',
        'bio',
        'location',
        'website_url',
        'avatar_path',
        'banner_path',
        'social_links',
        'custom_links',
        'metadata',
        'is_public',
        'profile_views',
        'username_tokens',
        'username_tokens_granted_at',
        'username_changed_at',
        'published_at',
    ];

    /** @var array<string, string> The attributes that should be cast. */
    protected $casts = [
        'social_links' => 'array',
        'custom_links' => 'array',
        'metadata' => 'array',
        'is_public' => 'boolean',
        'profile_views' => 'integer',
        'username_tokens' => 'integer',
        'username_tokens_granted_at' => 'datetime',
        'username_changed_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * Get the table associated with the model.
     *
     * @return string Returns the name of the table associated with this model.
     */
    public function getTable(): string
    {
        // Return the name of the table associated with this model, which is configurable via the 'persona.tables.profiles' configuration option, defaulting to 'persona_profiles'.
        return config('persona.tables.profiles', 'persona_profiles');
    }

    /**
     * Get the route key name for the model.
     *
     * @return string Returns the name of the route key for this model.
     */
    public function getRouteKeyName(): string
    {
        // Return the name of the route key for this model, which is 'slug'.
        return 'slug';
    }

    /**
     * @return BelongsTo<Model, $this>
     */
    public function user(): BelongsTo
    {
        // Determine the user model class from configuration, falling back to the default user model if not specified.
        $userModel = config('persona.models.user') ?? config('auth.providers.users.model');

        // Validate that the resolved user model is a string and is a subclass of the Eloquent Model class.
        return $this->belongsTo($userModel, 'user_id');
    }

    /**
     * Get the comments for this profile.
     *
     * @return HasMany<PersonaComment, $this>
     */
    public function comments(): HasMany
    {
        /** @var class-string<PersonaComment> $commentModel */
        $commentModel = config('persona.models.comment', PersonaComment::class);

        /** @var HasMany<PersonaComment, $this> $relationship */
        $relationship = $this->hasMany($commentModel, 'persona_id');

        // Return the relationship to the PersonaComment model, allowing for retrieval of comments associated with this profile.
        return $relationship;
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePublic(Builder $query): Builder
    {
        // Return only profiles that are marked as public.
        return $query->where('is_public', true);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePublished(Builder $query): Builder
    {
        // Return only profiles that have a published_at date that is in the past.
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeVisible(Builder $query): Builder
    {
        // Return only profiles that are public and have a published_at date that is in the past.
        return $query->where('is_public', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Determine whether this Persona profile is visible to the public.
     *
     * @return bool Returns true if the profile is public and published, false otherwise.
     */
    public function isVisible(): bool
    {
        // Check if the profile is public and has a published_at date that is in the past.
        return $this->is_public && $this->published_at !== null && $this->published_at->isPast();
    }

    /**
     * Increment the profile views count for this Persona profile.
     *
     * @return bool Returns true if the increment operation was successful, false otherwise.
     */
    public function recordView(): bool
    {
        // Increment the profile_views attribute by 1 and return true if the operation was successful.
        return $this->increment('profile_views') > 0;
    }

    /**
     * Get the current number of username tokens available for this Persona profile.
     *
     * @return int Returns the number of username tokens available.
     */
    public function usernameTokens(): int
    {
        // Return the current number of username tokens available for this Persona profile.
        return $this->syncUsernameTokens();
    }

    /**
     * Synchronize the username token balance for this Persona profile based on the configured token earning rules.
     *
     * @return int Returns the updated number of username tokens available.
     */
    public function syncUsernameTokens(): int
    {
        // Check if username tokens are enabled in the configuration; if not, return the current token count without modification.
        if (! (bool) config('persona.usernames.enabled', true)) {
            return (int) $this->username_tokens;
        }

        // Retrieve the configured maximum number of tokens, tokens earned per interval, and the interval duration in months from the configuration.
        $maxTokens = max(0, (int) config('persona.usernames.max_tokens', 2));
        $tokensPerInterval = max(0, (int) config('persona.usernames.tokens_per_interval', 1));
        $intervalMonths = max(0, (int) config('persona.usernames.token_interval_months', 6));

        // If any of the configuration values are zero, return the current token count without modification.
        if ($maxTokens === 0 || $tokensPerInterval === 0 || $intervalMonths === 0) {
            return (int) $this->username_tokens;
        }

        // Calculate the current token count, ensuring it does not exceed the configured maximum.
        $currentTokens = min((int) $this->username_tokens, $maxTokens);

        // If the current token count is already at or above the maximum, update the stored token count if necessary and return it.
        if ($currentTokens >= $maxTokens) {
            if ($this->username_tokens !== $currentTokens) {
                $this->forceFill(['username_tokens' => $currentTokens])->save();
            }

            // Return the current token count, which is at or above the maximum allowed.
            return $currentTokens;
        }

        // Determine the last granted date for username tokens, falling back to the profile's creation date or the current time if not set.
        $lastGrantedAt = $this->username_tokens_granted_at ?? $this->created_at ?? now();
        $cursor = $lastGrantedAt->copy();
        $intervals = 0;

        // Loop to calculate how many token earning intervals have passed since the last granted date, incrementing the cursor by the configured interval duration in months.
        while ($cursor->copy()->addMonthsNoOverflow($intervalMonths)->lte(now())) {
            $cursor->addMonthsNoOverflow($intervalMonths);
            $intervals++;
        }

        // If no intervals have passed since the last granted date, return the current token count without modification.
        if ($intervals === 0) {
            return $currentTokens;
        }

        // Calculate the total number of tokens earned based on the number of intervals that have passed and the configured tokens earned per interval.
        $earnedTokens = $intervals * $tokensPerInterval;
        $newTokens = min($maxTokens, $currentTokens + $earnedTokens);

        // Update the profile's username token count and the last granted date to reflect the newly earned tokens, and save the changes to the database.
        $this->forceFill([
            'username_tokens' => $newTokens,
            'username_tokens_granted_at' => $cursor,
        ])->save();

        // Return the updated number of username tokens available for this Persona profile.
        return $newTokens;
    }

    /**
     * Get the next date when a username token will be granted for this Persona profile.
     *
     * @return Carbon|null Returns the next token grant date or null if tokens are disabled or maxed out.
     */
    public function nextUsernameTokenAt(): ?Carbon
    {
        // Check if username tokens are enabled in the configuration; if not, return null.
        if (! (bool) config('persona.usernames.enabled', true)) {
            return null;
        }

        // Check if the profile has already reached the maximum number of username tokens; if so, return null.
        if ($this->usernameTokens() >= (int) config('persona.usernames.max_tokens', 2)) {
            return null;
        }

        // Retrieve the configured interval in months for granting username tokens.
        $intervalMonths = max(0, (int) config('persona.usernames.token_interval_months', 6));

        // If the interval is zero, return null as no tokens will be granted.
        if ($intervalMonths === 0) {
            return null;
        }

        // Determine the last granted date for username tokens, falling back to the profile's creation date or the current time if not set.
        $lastGrantedAt = $this->username_tokens_granted_at ?? $this->created_at ?? now();

        // Calculate and return the next date when a username token will be granted by adding the configured interval to the last granted date.
        return $lastGrantedAt->copy()->addMonthsNoOverflow($intervalMonths);
    }

    /**
     * Determine whether this profile can change its username based on the current token balance and configuration.
     *
     * @return bool Returns true if the profile can change its username, false otherwise.
     */
    public function canChangeUsername(): bool
    {
        // Check if username changes are enabled in the configuration; if not, return false.
        if (! (bool) config('persona.usernames.enabled', true)) {
            return false;
        }

        // Check if the profile has enough username tokens to cover the cost of changing the username.
        return $this->usernameTokens() >= max(0, (int) config('persona.usernames.token_cost', 1));
    }

    /**
     * Change the public username (slug) for this Persona profile.
     *
     * @param  string  $username  The new username to set.
     * @param  bool  $spendToken  Whether to spend a token for the change (default: true).
     * @return bool Returns true when the username was successfully changed.
     *
     * @throws LogicException If the profile does not have enough username tokens to make the change.
     */
    public function changeUsername(string $username, bool $spendToken = true): bool
    {
        // Normalize the username to ensure consistent formatting for comparison.
        $username = $this->normalizeUsername($username);

        // If the normalized username is the same as the current slug, no change is needed, so return false.
        if ($username === $this->slug) {
            return false;
        }

        // Validate the new username to ensure it meets the configured requirements (length, format, uniqueness, etc.).
        $this->validateUsername($username);

        // If spending a token is required for the change, check if the profile has enough tokens and deduct the cost.
        if ($spendToken) {
            $tokenCost = max(0, (int) config('persona.usernames.token_cost', 1));

            // Check if the profile has enough username tokens to cover the cost of the change.
            if ($this->usernameTokens() < $tokenCost) {
                throw new LogicException('This profile does not have enough username tokens.');
            }

            // Deduct the token cost from the profile's username tokens, ensuring it does not go below zero.
            $this->username_tokens = max(0, (int) $this->username_tokens - $tokenCost);
        }

        // Update the profile's slug to the new username and record the time of the change.
        $this->slug = $username;
        $this->username_changed_at = now();

        // Save the changes to the database and return the result of the save operation.
        return $this->save();
    }

    /**
     * Determine whether a given username is available for use.
     *
     * @param  string  $username  The username to check for availability.
     * @return bool Returns true if the username is available, false otherwise.
     */
    public function usernameIsAvailable(string $username): bool
    {
        // Normalize the username to ensure consistent formatting for comparison.
        $username = $this->normalizeUsername($username);

        // If the normalized username is the same as the current slug, it is considered available.
        if ($username === $this->slug) {
            return true;
        }

        // If username uniqueness is not enforced in the configuration, consider the username available.
        if (! (bool) config('persona.usernames.unique', true)) {
            return true;
        }

        // Check the database to see if any other Persona profile already has the same slug (username).
        return ! static::query()
            ->where('slug', $username)
            ->when($this->exists, fn (Builder $query): Builder => $query->whereKeyNot($this->getKey()))
            ->exists();
    }

    /**
     * Generate the public URL for the avatar image of this Persona profile.
     *
     * @param  string|null  $disk  The storage disk to use for generating the URL. If null, the default disk from configuration will be used.
     * @return string|null Returns the URL for the avatar image or null if no avatar is set.
     */
    public function avatarUrl(?string $disk = null): ?string
    {
        // Check if the avatar_path is set; if not, return null.
        if (! $this->avatar_path) {
            return null;
        }

        // Generate the URL for the avatar image using the specified disk or the default disk from configuration.
        return Storage::disk($disk ?? config('persona.storage.disk', 'public'))->url($this->avatar_path);
    }

    /**
     * Generate the public URL for the banner image of this Persona profile.
     *
     * @param  string|null  $disk  The storage disk to use for generating the URL. If null, the default disk from configuration will be used.
     * @return string|null Returns the URL for the banner image or null if no banner is set.
     */
    public function bannerUrl(?string $disk = null): ?string
    {
        // Check if the banner_path is set; if not, return null.
        if (! $this->banner_path) {
            return null;
        }

        // Generate the URL for the banner image using the specified disk or the default disk from configuration.
        return Storage::disk($disk ?? config('persona.storage.disk', 'public'))->url($this->banner_path);
    }

    /**
     * Generate the public URL for this Persona profile.
     *
     * @return string Returns the URL for the public profile page.
     */
    public function url(): string
    {
        // Generate the URL for the public profile page using the configured route name and the current Persona instance.
        return route(config('persona.routes.show_name', 'persona.show'), $this);
    }

    /**
     * Normalize a username by converting it to lowercase, trimming whitespace, and replacing spaces with hyphens.
     *
     * @param  string  $username  The username to normalize.
     * @return string Returns the normalized username.
     */
    protected function normalizeUsername(string $username): string
    {
        $username = Str::lower(trim($username));
        $username = preg_replace('/\s+/', '-', $username) ?? $username;

        return trim($username, '-');
    }

    /**
     * Validate a username against the configured requirements.
     *
     * @param  string  $username  The username to validate.
     *
     * @throws InvalidArgumentException If the username does not meet the requirements.
     */
    protected function validateUsername(string $username): void
    {
        // Retrieve the minimum and maximum length constraints for usernames from configuration, ensuring they are valid integers.
        $minLength = max(1, (int) config('persona.usernames.min_length', 3));
        $maxLength = max($minLength, (int) config('persona.usernames.max_length', 32));
        $reserved = config('persona.usernames.reserved', config('persona.slugs.reserved', []));
        $reserved = is_array($reserved) ? $reserved : [];
        $regex = config('persona.usernames.regex', '/^[a-z0-9_][a-z0-9_-]*[a-z0-9_]$/');

        // Validate the length of the username against the configured minimum and maximum lengths, throwing an exception if it does not meet the requirements.
        if (strlen($username) < $minLength || strlen($username) > $maxLength) {
            throw new InvalidArgumentException("Usernames must be between {$minLength} and {$maxLength} characters.");
        }

        // Validate the format of the username against the configured regular expression, throwing an exception if it does not match the expected pattern.
        if (is_string($regex) && preg_match($regex, $username) !== 1) {
            throw new InvalidArgumentException('The username format is invalid.');
        }

        // Check if the username is in the list of reserved usernames, throwing an exception if it is reserved.
        if (in_array($username, $reserved, true)) {
            throw new InvalidArgumentException('This username is reserved.');
        }

        // Check if the username is already taken by another profile, throwing an exception if it is not available.
        if (! $this->usernameIsAvailable($username)) {
            throw new InvalidArgumentException('This username is already taken.');
        }
    }

    /**
     * Get approved comments for this profile.
     *
     * @return HasMany<PersonaComment, $this>
     */
    public function approvedComments(): HasMany
    {
        /** @var HasMany<PersonaComment, $this> $relationship */
        $relationship = $this->comments()->approved();

        // Return the relationship to the PersonaComment model, filtered to only include comments that are approved.
        return $relationship;
    }

    /**
     * Get pinned comments for this profile.
     *
     * @return HasMany<PersonaComment, $this>
     */
    public function pinnedComments(): HasMany
    {
        /** @var HasMany<PersonaComment, $this> $relationship */
        $relationship = $this->comments()->pinned();

        // Return the relationship to the PersonaComment model, filtered to only include comments that are pinned.
        return $relationship;
    }

    /**
     * Add a comment to this Persona profile.
     *
     * @param  Model  $user  The user model instance representing the commenter.
     * @param  string  $body  The body of the comment.
     * @return PersonaComment Returns the newly created comment instance.
     *
     * @throws InvalidArgumentException If comments are disabled, the comment body is empty, or exceeds the maximum length.
     */
    public function addComment(Model $user, string $body): PersonaComment
    {
        // Check if comments are enabled in the configuration; if not, throw an exception.
        if (! config('persona.comments.enabled', true)) {
            throw new InvalidArgumentException('Persona comments are disabled.');
        }

        // Trim the comment body to remove leading and trailing whitespace.
        $body = trim($body);

        // Check if the comment body is empty after trimming; if so, throw an exception.
        if ($body === '') {
            throw new InvalidArgumentException('Comment body cannot be empty.');
        }

        // Retrieve the maximum allowed length for comments from the configuration, defaulting to 1000 characters if not set.
        $maxLength = (int) config('persona.comments.max_length', 1000);

        // Check if the comment body exceeds the maximum allowed length; if so, throw an exception.
        if (mb_strlen($body) > $maxLength) {
            throw new InvalidArgumentException("Comment body may not be greater than {$maxLength} characters.");
        }

        /** @var class-string<PersonaComment> $commentModel */
        $commentModel = config('persona.models.comment', PersonaComment::class);

        // Create a new comment associated with this Persona profile, setting the user ID, comment body, and approval status based on configuration.
        return $this->comments()->create([
            'user_id' => $user->getKey(),
            'body' => $body,
            'is_approved' => ! config('persona.comments.require_approval', false),
        ]);
    }
}
