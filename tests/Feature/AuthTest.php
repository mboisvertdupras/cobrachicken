<?php

use App\Models\User;

it('logs in by username', function () {
    $user = User::factory()->create(['username' => 'goose']);

    $this->post('/login', ['username' => 'goose', 'password' => 'password'])->assertRedirect('/');

    $this->assertAuthenticatedAs($user);
});

it('rejects a wrong password', function () {
    User::factory()->create(['username' => 'goose']);

    $this->post('/login', ['username' => 'goose', 'password' => 'wrong'])->assertSessionHasErrors('username');

    $this->assertGuest();
});

it('registers a user with a username', function () {
    $this->post('/register', [
        'username' => 'new_goose',
        'email' => 'goose@example.com',
        'password' => 'a-long-password',
        'password_confirmation' => 'a-long-password',
    ])->assertRedirect('/');

    $user = User::sole();
    expect($user->username)->toBe('new_goose')->and($user->karma)->toBe(1);
    $this->assertAuthenticatedAs($user);
});

it('rejects invalid usernames', function (string $username) {
    $this->post('/register', [
        'username' => $username,
        'email' => 'goose@example.com',
        'password' => 'a-long-password',
        'password_confirmation' => 'a-long-password',
    ])->assertSessionHasErrors(['username' => 'Usernames are 2 to 15 letters, digits, dashes or underscores.']);
})->with(['g', 'has space', 'way_too_long_username']);

it('shows login and create account forms on the login page', function () {
    $this->get('/login')->assertOk()->assertSee('Create Account');
});

it('serves the forgot password page', function () {
    $this->get('/forgot-password')->assertOk()->assertSee('Reset your password');
});
