<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
}
