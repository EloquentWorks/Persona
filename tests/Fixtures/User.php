<?php

namespace Tests\Fixtures;

use EloquentWorks\Persona\Traits\HasPersona;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasPersona;

    /** @var list<string> The attributes that are mass assignable. */
    protected $fillable = [
        'name',
        'email',
    ];
}
