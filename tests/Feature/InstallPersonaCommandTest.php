<?php

namespace EloquentWorks\Persona\Tests\Feature;

use EloquentWorks\Persona\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class InstallPersonaCommandTest extends TestCase
{
    #[Test]
    public function it_prints_the_route_snippet(): void
    {
        $this->artisan('persona:install')
            ->expectsOutputToContain('use Illuminate\Support\Facades\Route;')
            ->expectsOutputToContain('Route::persona();')
            ->assertSuccessful();
    }
}
