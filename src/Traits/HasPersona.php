<?php

namespace EloquentWorks\Persona\Traits;

use EloquentWorks\Persona\Models\Persona;
use EloquentWorks\Persona\Models\PersonaComment;
use EloquentWorks\Persona\Support\SlugGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Provides Persona profile functionality to Eloquent user models.
 */
trait HasPersona
{
    /**
     * Get the Persona profile associated with this model.
     *
     * @return HasOne Returns the profile relationship.
     */
    public function persona(): HasOne
    {
        // Return a one-to-one relationship with the Persona model, using the user_id foreign key.
        return $this->hasOne(config('persona.models.persona', Persona::class), 'user_id');
    }

    /**
     * Determine whether this model has a Persona profile.
     *
     * @return bool Returns true when a profile exists.
     */
    public function hasPersona(): bool
    {
        // Check if the Persona profile relationship exists for this model.
        return $this->persona()->exists();
    }

    /**
     * Create a Persona profile for this model.
     *
     * @param  array<string, mixed>  $attributes  The profile attributes.
     * @return Persona Returns the created Persona profile.
     */
    public function createPersona(array $attributes = []): Persona
    {
        // Ensure the user_id is set to the current model's primary key.
        if (! array_key_exists('slug', $attributes)) {
            $attributes['slug'] = app(SlugGenerator::class)->forModel($this, $attributes);
        }

        // Set default values for is_public, username_tokens, and username_tokens_granted_at if not provided.
        $attributes['is_public'] = $attributes['is_public'] ?? config('persona.visibility.default_public', true);
        $attributes['username_tokens'] = $attributes['username_tokens'] ?? config('persona.usernames.initial_tokens', 0);
        $attributes['username_tokens_granted_at'] = $attributes['username_tokens_granted_at'] ?? now();

        /** @var Persona $persona */
        $persona = $this->persona()->create($attributes);

        // Return the newly created Persona profile.
        return $persona;
    }

    /**
     * Update the Persona profile for this model, creating one when missing.
     *
     * @param  array<string, mixed>  $attributes  The profile attributes.
     * @return Persona Returns the updated or created Persona profile.
     */
    public function updatePersona(array $attributes = []): Persona
    {
        // Retrieve the existing Persona profile for this model.
        $profile = $this->persona()->first();

        // If no profile exists, create a new one with the provided attributes.
        if (! $profile) {
            // Create a new Persona profile if one does not exist.
            return $this->createPersona($attributes);
        }

        // Update the existing Persona profile with the provided attributes.
        $profile->forceFill($attributes)->save();

        // Return the updated Persona profile.
        return $profile;
    }

    /**
     * Get the public Persona URL for this model.
     *
     * @return string|null Returns the profile URL or null when no profile exists.
     */
    public function personaUrl(): ?string
    {
        // Retrieve the Persona profile for this model.
        $profile = $this->persona()->first();

        // Return the URL of the profile if it exists, otherwise return null.
        return $profile?->url();
    }

    /**
     * Change the Persona username for this model.
     *
     * @param  string  $username  The new username to set.
     * @param  bool  $spendToken  Whether to spend a token for the change (default: true).
     * @return bool Returns true when the username was successfully changed.
     */
    public function changePersonaUsername(string $username, bool $spendToken = true): bool
    {
        // Retrieve the Persona profile for this model.
        $profile = $this->persona()->first();

        // If no profile exists, return false to indicate the username change cannot be performed.
        if (! $profile) {
            return false;
        }

        // Delegate the username change to the Persona profile's changeUsername method.
        return $profile->changeUsername($username, $spendToken);
    }

    /**
     * Determine whether this model can change its Persona username.
     *
     * @return bool Returns true when the username can be changed.
     */
    public function canChangePersonaUsername(): bool
    {
        // Retrieve the Persona profile for this model.
        $profile = $this->persona()->first();

        // Return whether the profile allows username changes, or false if no profile exists.
        return $profile?->canChangeUsername() ?? false;
    }

    /**
     * Get the current username token balance for this model's Persona profile.
     *
     * @return int Returns the number of username tokens available.
     */
    public function personaUsernameTokens(): int
    {
        // Retrieve the Persona profile for this model.
        $profile = $this->persona()->first();

        // Return the number of username tokens, or 0 if no profile exists.
        return $profile?->usernameTokens() ?? 0;
    }

    /**
     * Get the configured user model key for slug generation.
     *
     * @return string Returns the fallback display value for this model.
     */
    public function personaSlugSource(): string
    {
        // Determine the source attribute for generating the slug based on configuration.
        $source = config('persona.slugs.source', 'name');

        // If the source is a string and the corresponding attribute exists and is filled, return its value as a string.
        if (is_string($source) && isset($this->{$source}) && filled($this->{$source})) {
            return (string) $this->{$source};
        }

        // If the source is a callable, invoke it with the current model instance and return its result as a string.
        if ($this instanceof Model && $this->getKey()) {
            return 'user-'.$this->getKey();
        }

        // Fallback to a default slug when no other source is available.
        return 'persona';
    }

    /**
     * Add a comment to a Persona profile.
     *
     * @param  Persona  $persona  The Persona profile to comment on.
     * @param  string  $body  The content of the comment.
     * @return PersonaComment Returns the created PersonaComment instance.
     */
    public function commentOnPersona(Persona $persona, string $body): PersonaComment
    {
        // Delegate the comment creation to the Persona profile's addComment method.
        return $persona->addComment($this, $body);
    }
}
