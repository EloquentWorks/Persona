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
        // Create the persona_username_histories table with necessary columns and relationships.
        Schema::create(config('persona.tables.username_histories', 'persona_username_histories'), function (Blueprint $table): void {
            // Primary key for the username histories table, automatically incrementing.
            $table->id();

            // Store the foreign key to the persona_profiles table, linking the username history to a specific persona profile. This column is indexed for efficient querying.
            $table->foreignId('persona_id')
                ->constrained(config('persona.tables.profiles', 'persona_profiles'))
                ->cascadeOnDelete();

            // Store the old and new slugs as strings, which can be used to track changes in usernames. Both columns are indexed for efficient querying.
            $table->string('old_slug')->index();
            $table->string('new_slug')->index();

            // Store the foreign key to the users table, linking the username history to the user who made the change. This column is nullable to allow for changes made by non-user entities (e.g., system processes).
            $table->nullableMorphs('changed_by');

            // Store the reason for the username change as a string, which can be used for auditing purposes. This column is nullable to allow for changes made without a specified reason.
            $table->string('reason')->nullable();

            // Store additional metadata about the username change in a JSON column, allowing for flexible storage of various attributes related to the change. This column is nullable to accommodate changes that may not have any additional metadata.
            $table->json('metadata')->nullable();

            // Timestamps for when the username change was made, automatically managed by Eloquent.
            $table->timestamps();

            // Add indexes to optimize queries filtering by persona_id and created_at, as well as a unique constraint to ensure that each persona can only have one history entry for the same old slug.
            $table->index(['persona_id', 'created_at']);
            $table->unique(['old_slug', 'persona_id'], 'persona_username_history_old_slug_persona_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void Returns nothing.
     */
    public function down(): void
    {
        // Drop the persona_username_histories table if it exists.
        Schema::dropIfExists(config('persona.tables.username_histories', 'persona_username_histories'));
    }
};
