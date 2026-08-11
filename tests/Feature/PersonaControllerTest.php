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

    #[Test]
    public function it_can_render_a_private_profile_placeholder(): void
    {
        config()->set('persona.visibility.private_profile_response', 'view');

        $user = createUser(['name' => 'Secret Nick']);

        $user->createPersona([
            'slug' => 'hidden-nick',
            'display_name' => 'Private Display Name',
            'headline' => 'Private headline',
            'bio' => 'Private biography',
            'is_public' => false,
        ]);

        $this->get('/@hidden-nick')
            ->assertOk()
            ->assertSee('This profile is private')
            ->assertSee('This user has chosen to keep their profile private.')
            ->assertDontSee('Private Display Name')
            ->assertDontSee('Private headline')
            ->assertDontSee('Private biography')
            ->assertDontSee('Secret Nick');
    }

    #[Test]
    public function private_profile_placeholder_does_not_increment_profile_views(): void
    {
        config()->set('persona.visibility.private_profile_response', 'view');

        $user = createUser(['name' => 'Nick']);

        $profile = $user->createPersona([
            'slug' => 'hidden-view-count',
            'is_public' => false,
        ]);

        $this->get('/@hidden-view-count')->assertOk();

        $this->assertSame(0, $profile->refresh()->profile_views);
    }

    #[Test]
    public function an_owner_can_view_their_own_private_profile(): void
    {
        $user = createUser(['name' => 'Nick']);

        $user->createPersona([
            'slug' => 'my-private-profile',
            'display_name' => 'Nick Private',
            'headline' => 'Owner-only headline',
            'is_public' => false,
        ]);

        $this->actingAs($user)
            ->get('/@my-private-profile')
            ->assertOk()
            ->assertSee('Nick Private')
            ->assertSee('Owner-only headline');
    }

    #[Test]
    public function owner_access_to_private_profile_can_be_disabled(): void
    {
        config()->set('persona.visibility.owner_can_view_private', false);

        $user = createUser(['name' => 'Nick']);

        $user->createPersona([
            'slug' => 'owner-hidden',
            'is_public' => false,
        ]);

        $this->actingAs($user)
            ->get('/@owner-hidden')
            ->assertNotFound();
    }
}
