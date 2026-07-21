<?php

namespace EloquentWorks\Persona\Tests\Unit;

use EloquentWorks\Persona\Support\ReservedUsername;
use EloquentWorks\Persona\Tests\TestCase;

final class ReservedUsernameTest extends TestCase
{
    public function test_default_reserved_names_are_available(): void
    {
        $this->assertContains('admin', ReservedUsername::all());
        $this->assertContains('support', ReservedUsername::all());
    }

    public function test_slug_normalization_is_applied(): void
    {
        config()->set('persona.usernames.reserved', ['My Admin']);

        $this->assertTrue(ReservedUsername::isReserved('my-admin'));
    }
}
