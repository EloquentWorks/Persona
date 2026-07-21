<?php

namespace EloquentWorks\Persona\Tests\Feature;

use EloquentWorks\Persona\Tests\TestCase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;

class PersonaControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::persona();
    }

    #[Test]
    public function it_shows_a_public_profile(): void
    {
        $user = createUser(['name' => 'Nick']);

        $user->createPersona([
            'slug' => 'nick',
            'display_name' => 'Nick',
            'headline' => 'Laravel Package Builder',
            'published_at' => now(),
        ]);

        $this->get('/@nick')
            ->assertOk()
            ->assertSee('Nick')
            ->assertSee('Laravel Package Builder');
    }

    #[Test]
    public function it_does_not_show_private_profiles(): void
    {
        $user = createUser(['name' => 'Nick']);

        $user->createPersona([
            'slug' => 'hidden-nick',
            'is_public' => false,
        ]);

        $this->get('/@hidden-nick')->assertNotFound();
    }

    #[Test]
    public function it_increments_profile_views(): void
    {
        $user = createUser(['name' => 'Nick']);

        $profile = $user->createPersona([
            'slug' => 'viewed-nick',
            'published_at' => now(),
        ]);

        $this->get('/@viewed-nick')->assertOk();

        $this->assertSame(1, $profile->refresh()->profile_views);
    }
}
