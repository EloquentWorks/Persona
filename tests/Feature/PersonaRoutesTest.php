<?php

namespace EloquentWorks\Persona\Tests\Feature;

use EloquentWorks\Persona\Http\Controllers\PersonaController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use EloquentWorks\Persona\Tests\TestCase;

class PersonaRoutesTest extends TestCase
{
    #[Test]
    public function it_registers_the_persona_route_macro(): void
    {
        $this->assertTrue(Router::hasMacro('persona'));
    }

    #[Test]
    public function it_registers_default_persona_routes(): void
    {
        Route::persona();

        $route = Route::getRoutes()->getByName('persona.show');

        $this->assertNotNull($route);
        $this->assertContains('GET', $route->methods());
        $this->assertSame('@{persona}', $route->uri());
        $this->assertSame(PersonaController::class, $route->getControllerClass());
        $this->assertSame('show', $route->getActionMethod());
    }

    #[Test]
    public function it_accepts_custom_route_options(): void
    {
        Route::persona([
            'prefix' => 'profiles',
            'path' => '{persona}',
            'name' => 'profiles.',
            'middleware' => ['web'],
        ]);

        $route = Route::getRoutes()->getByName('profiles.show');

        $this->assertNotNull($route);
        $this->assertSame('profiles/{persona}', $route->uri());
        $this->assertContains('web', $route->gatherMiddleware());
    }
}
