<?php

namespace EloquentWorks\Persona\Tests\Unit;

use EloquentWorks\Persona\Support\SlugGenerator;
use EloquentWorks\Persona\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

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
