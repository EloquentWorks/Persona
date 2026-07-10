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
        $persona = new Persona();

        $persona->forceFill([
            'social_links' => ['github' => 'https://github.com/example'],
            'custom_links' => [['label' => 'Website', 'url' => 'https://example.com']],
            'metadata' => ['theme' => 'dark'],
            'is_public' => 1,
            'profile_views' => '5',
            'published_at' => now(),
        ]);

        $this->assertIsArray($persona->social_links);
        $this->assertIsArray($persona->custom_links);
        $this->assertIsArray($persona->metadata);
        $this->assertTrue($persona->is_public);
        $this->assertSame(5, $persona->profile_views);
        $this->assertTrue($persona->published_at->isToday());
    }
}
