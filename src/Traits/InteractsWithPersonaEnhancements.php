<?php

namespace EloquentWorks\Persona\Traits;

use EloquentWorks\Persona\Events\PersonaBadgeAwarded;
use EloquentWorks\Persona\Events\PersonaUsernameChanged;
use EloquentWorks\Persona\Models\Persona;
use EloquentWorks\Persona\Models\PersonaBadge;
use EloquentWorks\Persona\Models\PersonaUsernameHistory;
use EloquentWorks\Persona\Models\PersonaView;
use EloquentWorks\Persona\Support\ProfileCompleteness;
use EloquentWorks\Persona\Support\ReservedUsername;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Adds optional v1.1 profile features without removing existing Persona behavior.
 *
 * @mixin Persona
 */
trait InteractsWithPersonaEnhancements
{
    /**
     * Define a one-to-many relationship to the PersonaView model.
     *
     * @return HasMany<PersonaView, $this>
     */
    public function views(): HasMany
    {
        /** @var HasMany<PersonaView, $this> $relationship */
        $relationship = $this->hasMany(PersonaView::class, 'persona_id');

        // Add a global scope to order the views by the most recent first.
        return $relationship;
    }

    /**
     * Define a one-to-many relationship to the PersonaUsernameHistory model.
     *
     * @return HasMany<PersonaUsernameHistory, $this>
     */
    public function usernameHistories(): HasMany
    {
        /** @var HasMany<PersonaUsernameHistory, $this> $relationship */
        $relationship = $this->hasMany(PersonaUsernameHistory::class, 'persona_id');

        // Add a global scope to order the username histories by the most recent first.
        return $relationship;
    }

    /**
     * Define a one-to-many relationship to the PersonaBadge model.
     *
     * @return HasMany<PersonaBadge, $this>
     */
    public function badges(): HasMany
    {
        /** @var HasMany<PersonaBadge, $this> $relationship */
        $relationship = $this->hasMany(PersonaBadge::class, 'persona_id');

        // Add a global scope to order the badges by the most recent first.
        return $relationship;
    }

    /**
     * Define a one-to-many relationship to the PersonaBadge model, filtered to only include public and active badges, ordered by sort_order.
     *
     * @return HasMany<PersonaBadge, $this>
     */
    public function publicBadges(): HasMany
    {
        /** @var HasMany<PersonaBadge, $this> $relationship */
        $relationship = $this->badges()->public()->active()->orderBy('sort_order');

        // Add a global scope to order the public badges by sort_order.
        return $relationship;
    }

    /**
     * Record a detailed profile view and keep the existing profile_views counter in sync.
     *
     * @param  array<string, mixed>  $metadata
     */
    public function recordDetailedView(?Request $request = null, ?Authenticatable $viewer = null, array $metadata = [], ?string $source = null): PersonaView
    {
        // Use the provided request or get the current request instance if not provided.
        $request ??= request();

        /** @var PersonaView $view */
        $view = DB::transaction(function () use ($request, $viewer, $metadata, $source): PersonaView {
            $this->increment('profile_views');
            $this->forceFill(['last_viewed_at' => now()])->save();

            // Create a new PersonaView record with the provided details, including viewer ID, session ID, IP hash, user agent hash, referer, source, metadata, and viewed_at timestamp.
            return $this->views()->create([
                'viewer_id' => $viewer instanceof Model ? $viewer->getKey() : null,
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'ip_hash' => $this->hashNullable((string) $request->ip()),
                'user_agent_hash' => $this->hashNullable((string) $request->userAgent()),
                'referer' => $request->headers->get('referer'),
                'source' => $source,
                'metadata' => $metadata === [] ? null : $metadata,
                'viewed_at' => now(),
            ]);
        });

        // Fire an event to notify that a detailed profile view has been recorded.
        return $view;
    }

    /**
     * Get the count of unique profile views in the last X days.
     *
     * @param  int  $days  The number of days to look back for unique views.
     * @return int Returns the count of unique profile views.
     */
    public function uniqueViewsCount(int $days = 30): int
    {
        // Use the views relationship to query for unique views in the last X days, counting distinct IP hashes.
        return $this->views()
            ->recent($days)
            ->select('ip_hash')
            ->whereNotNull('ip_hash')
            ->distinct()
            ->count('ip_hash');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function awardBadge(string $name, array $attributes = [], ?Authenticatable $awardedBy = null): PersonaBadge
    {
        // Use updateOrCreate to either update an existing badge or create a new one with the provided name and attributes.
        $badge = $this->badges()->updateOrCreate(
            ['name' => Str::slug($name)],
            array_merge([
                'label' => Str::headline($name),
                'is_public' => true,
                'awarded_at' => now(),
                'revoked_at' => null,
            ], $attributes)
        );

        // If an awardedBy user is provided, associate it with the badge and save the changes.
        if ($awardedBy instanceof Model) {
            $badge->awardedBy()->associate($awardedBy);
            $badge->save();
        }

        // Fire an event to notify that a badge has been awarded to the persona.
        event(new PersonaBadgeAwarded($this, $badge, $awardedBy));

        // Return the awarded badge instance.
        return $badge;
    }

    /**
     * Revoke a badge by its name.
     *
     * @param  string  $name  The name of the badge to revoke.
     * @return bool Returns true if the badge was successfully revoked, false otherwise.
     */
    public function revokeBadge(string $name): bool
    {
        // Use the badges relationship to find the badge by its name and update its revoked_at timestamp to the current time.
        return (bool) $this->badges()
            ->named(Str::slug($name))
            ->active()
            ->update(['revoked_at' => now()]);
    }

    /**
     * Check if the persona has an active badge by its name.
     *
     * @param  string  $name  The name of the badge to check.
     * @return bool Returns true if the badge is active, false otherwise.
     */
    public function hasActiveBadge(string $name): bool
    {
        // Use the badges relationship to check if an active badge with the given name exists for the persona.
        return $this->badges()
            ->named(Str::slug($name))
            ->active()
            ->exists();
    }

    public function safeSeoTitle(): string
    {
        $title = (string) ($this->getAttribute('seo_title') ?: $this->getAttribute('display_name') ?: $this->getAttribute('slug'));

        return Str::limit(strip_tags($title), 70, '');
    }

    public function safeSeoDescription(): string
    {
        $description = (string) ($this->getAttribute('seo_description') ?: $this->getAttribute('bio') ?: $this->getAttribute('headline') ?: '');

        return Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($description)) ?: ''), 160, '');
    }

    /**
     * @return array<string, string>
     */
    public function socialShareMeta(): array
    {
        return [
            'title' => $this->safeSeoTitle(),
            'description' => $this->safeSeoDescription(),
            'image' => (string) ($this->avatarUrl() ?? $this->bannerUrl() ?? ''),
            'url' => (string) $this->url(),
        ];
    }

    /**
     * @return array<string, bool>
     */
    public function completionChecklist(): array
    {
        return ProfileCompleteness::checklist($this);
    }

    public function refreshCompleteness(): int
    {
        $score = ProfileCompleteness::score($this);

        $this->forceFill([
            'profile_completeness_score' => $score,
            'profile_completed_at' => $score >= 100 ? ($this->profile_completed_at ?? now()) : null,
        ])->save();

        return $score;
    }

    /**
     * Change the username while preserving the old slug in username history.
     *
     * @param  array<string, mixed>  $metadata
     */
    public function changeUsernameWithHistory(string $username, bool $spendToken = true, ?Authenticatable $changedBy = null, ?string $reason = null, array $metadata = []): bool
    {
        $normalized = Str::slug($username);

        if ($normalized === '') {
            throw new InvalidArgumentException('The username must contain at least one valid character.');
        }

        if (ReservedUsername::isReserved($normalized)) {
            throw new InvalidArgumentException("The username [{$normalized}] is reserved.");
        }

        $oldSlug = (string) $this->getAttribute('slug');

        if ($oldSlug === $normalized) {
            return false;
        }

        $changed = DB::transaction(function () use ($normalized, $spendToken, $changedBy, $reason, $metadata, $oldSlug): bool {
            $changed = $this->changeUsername($normalized, $spendToken);

            if (! $changed) {
                return false;
            }

            $history = $this->usernameHistories()->create([
                'old_slug' => $oldSlug,
                'new_slug' => $normalized,
                'reason' => $reason,
                'metadata' => $metadata === [] ? null : $metadata,
            ]);

            if ($changedBy instanceof Model) {
                $history->changedBy()->associate($changedBy);
                $history->save();
            }

            return true;
        });

        if ($changed) {
            event(new PersonaUsernameChanged($this, $oldSlug, $normalized, $changedBy));
        }

        return $changed;
    }

    public function hashNullable(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $key = (string) config('app.key');

        return hash_hmac('sha256', $value, $key);
    }
}
