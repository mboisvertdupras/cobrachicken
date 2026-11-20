<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'username' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9_-]{2,15}$/',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (is_string($value) && User::usernameIs($value)->exists()) {
                        $fail('validation.unique')->translate();
                    }
                },
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ], [
            'username.regex' => 'Usernames are 2 to 15 letters, digits, dashes or underscores.',
        ])->validate();

        return User::create([
            'username' => $input['username'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
