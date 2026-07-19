<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void Returns nothing.
     */
    public function up(): void
    {
        // Create the persona_views table with necessary columns and relationships.
        Schema::create(config('persona.tables.views', 'persona_views'), function (Blueprint $table): void {
            // Primary key for the views table, automatically incrementing.
            $table->id();

            // Foreign key to the persona_profiles table, linking the view to a specific persona profile.
            $table->foreignId('persona_id')
                ->constrained(config('persona.tables.profiles', 'persona_profiles'))
                ->cascadeOnDelete();

            // Foreign key to the users table, linking the view to the user who viewed the profile. Nullable to allow for anonymous views.
            $table->foreignId('viewer_id')
                ->nullable()
                ->constrained(config('persona.tables.users', 'users'))
                ->nullOnDelete();

            // Store the session ID of the viewer, allowing for tracking of views by session. Nullable to allow for anonymous views.
            $table->string('session_id')->nullable()->index();

            // Store the IP address of the viewer, allowing for tracking of views by IP address. Nullable to allow for anonymous views.
            $table->string('ip_hash')->nullable()->index();

            // Store the user agent string of the viewer, allowing for tracking of views by device/browser. Nullable to allow for anonymous views.
            $table->string('user_agent_hash')->nullable();

            // Store the referer URL of the view, allowing for tracking of where the view originated from. Nullable to allow for views that may not have a referer.
            $table->string('referer')->nullable();

            // Store the source of the view, allowing for tracking of where the view originated from (e.g., search engine, social media, etc.). Nullable to allow for views that may not have a specific source.
            $table->string('source')->nullable()->index();

            // Store additional metadata about the view in a JSON column, allowing for flexible storage of various attributes related to the view. This column is nullable to accommodate views that may not have any additional metadata.
            $table->json('metadata')->nullable();

            // Timestamp for when the view occurred, indexed for efficient querying.
            $table->timestamp('viewed_at')->index();

            // Timestamps for when the view was created and last updated, automatically managed by Eloquent.
            $table->timestamps();

            // Indexes to optimize queries filtering by persona_id and viewed_at, as well as by persona_id and viewer_id.
            $table->index(['persona_id', 'viewed_at']);
            $table->index(['persona_id', 'viewer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void Returns nothing.
     */
    public function down(): void
    {
        // Drop the persona_views table if it exists, effectively reversing the migration.
        Schema::dropIfExists(config('persona.tables.views', 'persona_views'));
    }
};
