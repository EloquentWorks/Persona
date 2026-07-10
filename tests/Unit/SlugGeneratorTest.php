<?php

namespace Tests\Unit;

use EloquentWorks\Persona\Support\SlugGenerator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SlugGeneratorTest extends TestCase
{
    #[Test]
    public function it_generates_unique_slugs(): void
    {
        $user = createUser(['name' => 'Nick']);
        $user->createPersona(['slug' => 'nick']);

        $slug = app(SlugGenerator::class)->unique('Nick');

        $this->assertSame('nick-2', $slug);
    }

    #[Test]
    public function it_avoids_reserved_slugs(): void
    {
        $slug = app(SlugGenerator::class)->unique('admin');

        $this->assertSame('admin-profile', $slug);
    }
}
