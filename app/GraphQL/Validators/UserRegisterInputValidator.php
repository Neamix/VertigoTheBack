<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class UserRegisterInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            [
                'name' => ['required'],
                'email' => ['required','email','unique:users,email'],
                'password' => ['required','min:8']
            ]
        ];
    }
}
