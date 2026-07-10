<?php

namespace EloquentWorks\Persona\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * This model is used to manage user profiles, including their display name, bio, social links, and other related information.
 */
class Persona extends Model
{
    /** @var list<string> The attributes that are mass assignable. */
    protected $fillable = [
        'user_id', 'slug', 'display_name', 'headline', 'bio', 'location',
        'website_url', 'avatar_path', 'banner_path', 'social_links',
        'custom_links', 'metadata', 'is_public', 'profile_views', 'published_at',
    ];

    /** @var array<string, string> The attributes that should be cast. */
    protected $casts = [
        'social_links' => 'array', 'custom_links' => 'array', 'metadata' => 'array',
        'is_public' => 'boolean', 'profile_views' => 'integer', 'published_at' => 'datetime',
    ];

    /**
     * Get the table name for the Persona model.
     *
     * @return string The name of the table associated with the Persona model.
     */
    public function getTable(): string
    {
        return config('persona.tables.profiles', 'persona_profiles');
    }

    /**
     * Use the slug for route model binding.
     *
     * @return string The name of the route key for this model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the user that owns this Persona profile.
     *
     * @return BelongsTo The relationship instance linking the Persona profile to the user model.
     */
    public function user(): BelongsTo
    {
        // Retrieve the user model class from the configuration, falling back to the default user model if not set.
        $userModel = config('persona.models.user') ?? config('auth.providers.users.model');

        // Return the relationship to the user model, using the user_id foreign key.
        return $this->belongsTo($userModel, 'user_id');
    }

    /**
     * Scope the query to public profiles.
     *
     * @param  Builder  $query  The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopePublic(Builder $query): Builder
    {
        // Return profiles that are marked as public.
        return $query->where('is_public', true);
    }

    /**
     * Scope the query to published profiles.
     *
     * @param  Builder  $query  The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopePublished(Builder $query): Builder
    {
        // Return profiles that have a published date and the published date is in the past.
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * Scope the query to profiles that can be viewed publicly.
     *
     * @param  Builder  $query  The query builder instance.
     * @return Builder The modified query builder instance.
     */
    public function scopeVisible(Builder $query): Builder
    {
        // Return profiles that are both public and published.
        return $query->public()->published();
    }

    /**
     * Determine if the profile is publicly visible.
     */
    public function isVisible(): bool
    {
        // Check if the profile is public, has a published date, and that the published date is in the past.
        return $this->is_public && $this->published_at !== null && $this->published_at->isPast();
    }

    /**
     * Increment the profile view count.
     *
     * @return bool Returns true if the increment was successful, false otherwise.
     */
    public function recordView(): bool
    {
        // Increment the profile_views attribute by 1 and save the model.
        return $this->increment('profile_views');
    }

    /**
     * Get the avatar URL.
     *
     * @param  string|null  $disk  The storage disk to use for generating the URL. If null, the default disk from configuration will be used.
     * @return string|null The URL of the avatar image, or null if no avatar path is set.
     */
    public function avatarUrl(?string $disk = null): ?string
    {
        // If there is no avatar path, return null
        if (! $this->avatar_path) {
            return null;
        }

        // Use the specified disk or fallback to the default disk from configuration
        return Storage::disk($disk ?? config('persona.storage.disk', 'public'))->url($this->avatar_path);
    }

    /**
     * Get the banner URL.
     *
     * @param  string|null  $disk  The storage disk to use for generating the URL. If null, the default disk from configuration will be used.
     * @return string|null The URL of the banner image, or null if no banner path is set.
     */
    public function bannerUrl(?string $disk = null): ?string
    {
        // If there is no banner path, return null
        if (! $this->banner_path) {
            return null;
        }

        // Use the specified disk or fallback to the default disk from configuration
        return Storage::disk($disk ?? config('persona.storage.disk', 'public'))->url($this->banner_path);
    }

    /**
     * Get the URL for the profile page.
     *
     * @return string The URL of the profile page.
     */
    public function url(): string
    {
        return route(config('persona.routes.show_name', 'persona.show'), $this);
    }
}
