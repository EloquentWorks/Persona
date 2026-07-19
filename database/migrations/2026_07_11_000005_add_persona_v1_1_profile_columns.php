<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the table name from the configuration, defaulting to 'persona_profiles' if not set.
        $tableName = config('persona.tables.profiles', 'persona_profiles');

        // Add new columns to the persona_profiles table if they do not already exist.
        Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
            // Add a new column for profile completion timestamp, nullable, if it does not already exist.
            if (! Schema::hasColumn($tableName, 'profile_completed_at')) {
                $table->timestamp('profile_completed_at')->nullable()->after('profile_views');
            }

            // Add a new column for profile completeness score, defaulting to 0, if it does not already exist.
            if (! Schema::hasColumn($tableName, 'profile_completeness_score')) {
                $table->unsignedTinyInteger('profile_completeness_score')->default(0)->after('profile_completed_at');
            }

            // Add a new column for last viewed timestamp, nullable, if it does not already exist.
            if (! Schema::hasColumn($tableName, 'last_viewed_at')) {
                $table->timestamp('last_viewed_at')->nullable()->after('profile_completeness_score');
            }

            // Add a new column for featured links, stored as JSON and nullable, if it does not already exist.
            if (! Schema::hasColumn($tableName, 'featured_links')) {
                $table->json('featured_links')->nullable()->after('custom_links');
            }

            // Add a new column for SEO title, stored as a string and nullable, if it does not already exist.
            if (! Schema::hasColumn($tableName, 'seo_title')) {
                $table->string('seo_title')->nullable()->after('featured_links');
            }

            // Add a new column for SEO description, stored as a string and nullable, if it does not already exist.
            if (! Schema::hasColumn($tableName, 'seo_description')) {
                $table->string('seo_description', 320)->nullable()->after('seo_title');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void Returns nothing.
     */
    public function down(): void
    {
        // Get the table name from the configuration, defaulting to 'persona_profiles' if not set.
        $tableName = config('persona.tables.profiles', 'persona_profiles');

        // Drop the added columns if they exist when rolling back the migration.
        Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
            foreach ([
                'seo_description',
                'seo_title',
                'featured_links',
                'last_viewed_at',
                'profile_completeness_score',
                'profile_completed_at',
            ] as $column) {
                // Drop the column if it exists in the table.
                if (Schema::hasColumn($tableName, $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
