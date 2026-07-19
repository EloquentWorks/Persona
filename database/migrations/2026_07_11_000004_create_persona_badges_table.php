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
        // Create the persona_badges table with necessary columns and relationships.
        Schema::create(config('persona.tables.badges', 'persona_badges'), function (Blueprint $table): void {
            // Primary key for the badges table, automatically incrementing.
            $table->id();

            // Store the foreign key to the persona_profiles table, linking the badge to a specific persona profile. This column is indexed for efficient querying.
            $table->foreignId('persona_id')
                ->constrained(config('persona.tables.profiles', 'persona_profiles'))
                ->cascadeOnDelete();

            // Store the name of the badge as a string, which can be used to identify the badge in the system. This column is indexed for efficient querying.
            $table->string('name');

            // Store the label of the badge as a string, which can be used for display purposes in the UI.
            $table->string('label')->nullable();

            // Store a description of the badge as text, which can be used for display purposes in the UI. This column is nullable to allow for badges that may not have a description.
            $table->text('description')->nullable();

            // Store the icon associated with the badge as a string, which can be used for display purposes in the UI. This column is nullable to allow for badges that may not have an icon.
            $table->string('icon')->nullable();

            // Store the color of the badge as a string, which can be used for display purposes in the UI. This column is nullable to allow for badges that may not have a specific color associated with them.
            $table->unsignedInteger('sort_order')->default(0);

            // Store a boolean indicating whether the badge is public or not. This column is indexed for efficient querying.
            $table->boolean('is_public')->default(true)->index();

            // Store a polymorphic relationship to the model that awarded the badge. This allows for flexibility in awarding badges from different models (e.g., users, admins, etc.).
            $table->nullableMorphs('awarded_by');

            // Store additional metadata about the badge in a JSON column, allowing for flexible storage of various attributes related to the badge. This column is nullable to accommodate badges that may not have any additional metadata.
            $table->json('metadata')->nullable();

            // Timestamps for when the badge was awarded, when it expires, and when it was revoked, all nullable and indexed for efficient querying.
            $table->timestamp('awarded_at')->nullable()->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('revoked_at')->nullable()->index();

            // Timestamps for when the badge was created and last updated, automatically managed by Eloquent.
            $table->timestamps();

            // Add a unique constraint to ensure that each persona can only have one badge with the same name.
            $table->unique(['persona_id', 'name'], 'persona_badges_persona_name_unique');
            $table->index(['persona_id', 'is_public', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void Returns nothing.
     */
    public function down(): void
    {
        // Drop the persona_badges table if it exists, effectively reversing the migration.
        Schema::dropIfExists(config('persona.tables.badges', 'persona_badges'));
    }
};
