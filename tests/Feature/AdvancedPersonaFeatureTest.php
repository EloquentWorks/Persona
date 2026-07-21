<?php

namespace Tests\Feature;

use EloquentWorks\Persona\Models\Persona;
use EloquentWorks\Persona\Models\PersonaBadge;
use EloquentWorks\Persona\Models\PersonaUsernameHistory;
use EloquentWorks\Persona\Models\PersonaView;
use EloquentWorks\Persona\Rules\ReservedPersonaUsername;
use EloquentWorks\Persona\Rules\SafeProfileUrl;
use EloquentWorks\Persona\Support\ProfileCompleteness;
use EloquentWorks\Persona\Support\ReservedUsername;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

final class AdvancedPersonaFeatureTest extends TestCase
{
    public function test_reserved_usernames_are_detected(): void
    {
        $this->assertTrue(ReservedUsername::isReserved('admin'));
        $this->assertTrue(ReservedUsername::isReserved('Support'));
        $this->assertFalse(ReservedUsername::isReserved('signal-nick'));
    }

    public function test_reserved_username_rule_fails_for_reserved_names(): void
    {
        $validator = Validator::make(
            ['slug' => 'admin'],
            ['slug' => [new ReservedPersonaUsername]]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_safe_profile_url_rule_rejects_javascript_urls(): void
    {
        $validator = Validator::make(
            ['website_url' => 'javascript:alert(1)'],
            ['website_url' => [new SafeProfileUrl]]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_profile_completeness_returns_score(): void
    {
        $persona = new Persona([
            'display_name' => 'Nick',
            'headline' => 'Laravel Developer',
            'bio' => 'Building packages.',
        ]);

        $this->assertGreaterThan(0, ProfileCompleteness::score($persona));
    }

    public function test_models_resolve_configured_table_names(): void
    {
        $this->assertSame('persona_views', (new PersonaView)->getTable());
        $this->assertSame('persona_username_histories', (new PersonaUsernameHistory)->getTable());
        $this->assertSame('persona_badges', (new PersonaBadge)->getTable());
    }
}
