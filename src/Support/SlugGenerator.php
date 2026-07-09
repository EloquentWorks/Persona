<?php

namespace EloquentWorks\Persona\Support;

use EloquentWorks\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class SlugGenerator
 *
 * This class is responsible for generating unique slugs for user profiles.
 * It ensures that the generated slugs are unique and do not conflict with reserved paths.
 */
class SlugGenerator
{
    /**
     * Generate a unique slug for a user model.
     *
     * @param  Model  $model  The model that owns the profile.
     * @param  array<string, mixed>  $attributes  The incoming profile attributes.
     * @return string Returns a unique profile slug.
     */
    public function forModel(Model $model, array $attributes = []): string
    {
        // Determine the source for the slug based on the provided attributes or model methods.
        $source = $attributes['display_name']
            ?? $attributes['slug']
            ?? (method_exists($model, 'personaSlugSource') ? $model->personaSlugSource() : null)
            ?? $model->getKey()
            ?? 'persona';

        // Generate and return a unique slug based on the determined source.
        return $this->unique((string) $source);
    }

    /**
     * Generate a unique slug from a string value.
     *
     * @param  string  $value  The source string.
     * @return string Returns a unique slug.
     */
    public function unique(string $value): string
    {
        // Retrieve the slug separator and maximum length from the configuration.
        $separator = (string) config('persona.slugs.separator', '-');
        $maxLength = (int) config('persona.slugs.max_length', 64);

        // Generate a base slug using Laravel's Str::slug method, ensuring it is not empty.
        $base = Str::slug($value, $separator);
        $base = $base !== '' ? $base : 'persona';
        $base = Str::limit($base, $maxLength, '');
        $base = $this->avoidReservedSlug($base);

        // Initialize the slug and counter for uniqueness checking.
        $slug = $base;
        $counter = 2;

        // Loop to ensure the slug is unique by appending a counter if necessary.
        while ($this->exists($slug)) {
            $suffix = $separator.$counter;
            $slug = Str::limit($base, max(1, $maxLength - strlen($suffix)), '').$suffix;
            $counter++;
        }

        // Return the unique slug.
        return $slug;
    }

    /**
     * Avoid reserved application paths.
     *
     * @param  string  $slug  The generated slug.
     * @return string Returns a safe slug.
     */
    protected function avoidReservedSlug(string $slug): string
    {
        // Retrieve the list of reserved slugs from the configuration, ensuring it is an array.
        $reserved = config('persona.slugs.reserved', []);
        $reserved = is_array($reserved) ? $reserved : [];

        // Check if the generated slug is in the list of reserved slugs and modify it if necessary.
        if (in_array($slug, $reserved, true)) {
            return $slug.'-profile';
        }

        // Return the original slug if it is not reserved.
        return $slug;
    }

    /**
     * Determine whether the slug already exists.
     *
     * @param  string  $slug  The slug to check.
     * @return bool Returns true when the slug is already used.
     */
    protected function exists(string $slug): bool
    {
        // Retrieve the Persona model class from the configuration, defaulting to the Persona class if not set.
        $personaModel = config('persona.models.persona', Persona::class);

        // Check if a record with the given slug already exists in the database.
        return $personaModel::query()->where('slug', $slug)->exists();
    }
}
