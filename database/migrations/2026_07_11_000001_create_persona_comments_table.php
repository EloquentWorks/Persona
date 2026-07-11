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
        // Create the persona_comments table with necessary columns and relationships.
        Schema::create(config('persona.tables.comments', 'persona_comments'), function (Blueprint $table): void {
            // Primary key for the comments table, automatically incrementing.
            $table->id();

            // Foreign key to the persona_profiles table, linking the comment to a specific persona profile.
            $table->foreignId('persona_id')
                ->constrained(config('persona.tables.profiles', 'persona_profiles'))
                ->cascadeOnDelete();

            // Foreign key to the users table, linking the comment to the user who made it.
            $table->foreignId('user_id')
                ->constrained(config('persona.tables.users', 'users'))
                ->cascadeOnDelete();

            // The body of the comment, stored as text to allow for longer content.
            $table->text('body');

            // Indicates whether the comment is approved for public display. Defaults to true.
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_pinned')->default(false);

            // Timestamp for when the comment was edited. Nullable to indicate that it may not have been edited.
            $table->timestamp('edited_at')->nullable();

            // Timestamps for when the comment was created and last updated, automatically managed by Eloquent.
            $table->timestamps();
            $table->softDeletes();

            // Indexes to optimize queries filtering by persona_id and approval/pinned status, as well as by user_id.
            $table->index(['persona_id', 'is_approved']);
            $table->index(['persona_id', 'is_pinned']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void Returns nothing.
     */
    public function down(): void
    {
        // Drop the persona_comments table if it exists, effectively rolling back the migration.
        Schema::dropIfExists(config('persona.tables.comments', 'persona_comments'));
    }
};
