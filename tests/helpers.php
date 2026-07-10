<?php

use Tests\Fixtures\User;

if (! function_exists('createUser')) {
    /**
     * Create a test user.
     *
     * @param  array<string, mixed>  $attributes  Optional attributes.
     * @return User Returns the created user.
     */
    function createUser(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Nick',
            'email' => uniqid('user_', true).'@example.com',
        ], $attributes));
    }
}
