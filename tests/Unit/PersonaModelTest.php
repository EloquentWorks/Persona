<?php

namespace Tests\Unit;

use EloquentWorks\Persona\Models\Persona;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonaModelTest extends TestCase
{
    #[Test]
    public function it_uses_the_configured_table_name(): void
    {
        config()->set('persona.tables.profiles', 'custom_profiles');

        $this->assertSame('custom_profiles', (new Persona)->getTable());
    }

    #[Test]
    public function it_casts_json_boolean_integer_and_date_columns(): void
    {
        $persona = new Persona;

        $persona->forceFill([
            'social_links' => ['github' => 'https://github.com/example'],
            'custom_links' => [['label' => 'Website', 'url' => 'https://example.com']],
            'metadata' => ['theme' => 'dark'],
            'is_public' => 1,
            'username_tokens' => '2',
            'username_tokens_granted_at' => now(),
            'username_changed_at' => now(),
            'profile_views' => '5',
            'published_at' => now(),
        ]);

        $this->assertIsArray($persona->social_links);
        $this->assertIsArray($persona->custom_links);
        $this->assertIsArray($persona->metadata);
        $this->assertTrue($persona->is_public);
        $this->assertSame(2, $persona->username_tokens);
        $this->assertTrue($persona->username_tokens_granted_at->isToday());
        $this->assertTrue($persona->username_changed_at->isToday());
        $this->assertSame(5, $persona->profile_views);
        $this->assertTrue($persona->published_at->isToday());
    }

    #[Test]
    public function it_can_change_a_username_with_a_token(): void
    {
        $persona = Persona::create([
            'user_id' => createUser(['name' => 'Nick'])->getKey(),
            'slug' => 'nick',
            'username_tokens' => 1,
            'username_tokens_granted_at' => now(),
        ]);

        $changed = $persona->changeUsername('Signal_Nick');

        $this->assertTrue($changed);
        $this->assertSame('signal_nick', $persona->refresh()->slug);
        $this->assertSame(0, $persona->username_tokens);
        $this->assertNotNull($persona->username_changed_at);
    }

    #[Test]
    public function it_grants_username_tokens_after_the_configured_interval(): void
    {
        config()->set('persona.usernames.token_interval_months', 6);
        config()->set('persona.usernames.tokens_per_interval', 1);
        config()->set('persona.usernames.max_tokens', 2);

        $persona = Persona::create([
            'user_id' => createUser(['name' => 'Nick'])->getKey(),
            'slug' => 'nick',
            'username_tokens' => 0,
            'username_tokens_granted_at' => now()->subMonthsNoOverflow(12),
        ]);

        $this->assertSame(2, $persona->usernameTokens());
        $this->assertSame(2, $persona->refresh()->username_tokens);
    }
}
