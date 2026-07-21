<?php

namespace EloquentWorks\Persona\Tests\Unit;

use EloquentWorks\Persona\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class HasPersonaTest extends TestCase
{
    #[Test]
    public function it_can_create_a_persona_profile(): void
    {
        $user = createUser(['name' => 'Nick']);

        $profile = $user->createPersona([
            'display_name' => 'Nick',
            'headline' => 'Developer',
        ]);

        $this->assertTrue($user->hasPersona());
        $this->assertSame('Nick', $profile->display_name);
        $this->assertSame('nick', $profile->slug);
    }

    #[Test]
    public function it_can_update_a_persona_profile(): void
    {
        $user = createUser(['name' => 'Nick']);

        $user->createPersona(['slug' => 'nick']);

        $profile = $user->updatePersona([
            'headline' => 'Laravel Developer',
        ]);

        $this->assertSame('Laravel Developer', $profile->headline);
    }

    #[Test]
    public function it_can_change_a_persona_username_from_the_user_model(): void
    {
        $user = createUser(['name' => 'Nick']);

        $user->createPersona([
            'slug' => 'nick',
            'username_tokens' => 1,
        ]);

        $this->assertTrue($user->canChangePersonaUsername());
        $this->assertTrue($user->changePersonaUsername('signal-nick'));
        $this->assertSame('signal-nick', $user->persona()->first()?->slug);
        $this->assertSame(0, $user->personaUsernameTokens());
    }
}
