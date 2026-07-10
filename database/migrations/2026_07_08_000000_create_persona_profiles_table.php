<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration class for creating the persona profiles table.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void Returns nothing.
     */
    public function up(): void
    {
        // Create the persona profiles table with the specified schema.
        Schema::create(config('persona.tables.profiles', 'persona_profiles'), function (Blueprint $table): void {
            // Primary key for the profiles table, automatically incrementing.
            $table->id();

            // Foreign key to the users table, linking each profile to a specific user.
            $table->foreignId('user_id')
                ->constrained(config('persona.tables.users', 'users'))
                ->cascadeOnDelete();

            // Unique slug for the profile, used for URL routing and identification.
            $table->string('slug')->unique();

            // Optional display name for the profile, which can be different from the user's actual name.
            $table->string('display_name')->nullable();

            // Optional headline for the profile, providing a brief description or tagline.
            $table->string('headline')->nullable();

            // Optional biography for the profile, allowing users to provide more detailed information about themselves.
            $table->text('bio')->nullable();

            // Optional location for the profile, indicating where the user is based or associated with.
            $table->string('location')->nullable();

            // Optional website URL for the profile, allowing users to link to their personal or professional websites.
            $table->string('website_url')->nullable();

            // Optional paths for avatar and banner images, allowing users to customize their profile appearance.
            $table->string('avatar_path')->nullable();

            // Optional path for a banner image, allowing users to customize their profile appearance.
            $table->string('banner_path')->nullable();

            // Status of the profile, indicating whether it is active, inactive, or in another state.
            $table->boolean('is_public')->default(true);

            // Optional status field for the profile, allowing for more granular control over the profile's state.
            $table->unsignedBigInteger('profile_views')->default(0);

            // Optional social links for the profile, stored as a JSON object to allow for flexible data structures.
            $table->json('social_links')->nullable();
            $table->json('metadata')->nullable();

            // Optional timestamp for when the profile was published, allowing for scheduling and visibility control.
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Indexes for optimizing queries on the user_id and is_public columns, improving performance for common queries.
            $table->index(['user_id', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void Returns nothing.
     */
    public function down(): void
    {
        // Drop the persona profiles table if it exists.
        Schema::dropIfExists(config('persona.tables.profiles', 'persona_profiles'));
    }
};
