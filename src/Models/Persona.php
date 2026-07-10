<?php

namespace EloquentWorks\Persona\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

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
 * @property bool $is_public
 * @property int $profile_views
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
        'user_id', 'slug', 'display_name', 'headline', 'bio', 'location',
        'website_url', 'avatar_path', 'banner_path', 'social_links',
        'custom_links', 'metadata', 'is_public', 'profile_views', 'published_at',
    ];

    /** @var array<string, string> The attributes that should be cast. */
    protected $casts = [
        'social_links' => 'array',
        'custom_links' => 'array',
        'metadata' => 'array',
        'is_public' => 'boolean',
        'profile_views' => 'integer',
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
        $userModel = config('persona.models.user') ?? config('auth.providers.users.model');

        return $this->belongsTo($userModel, 'user_id');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePublished(Builder $query): Builder
    {
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
}
