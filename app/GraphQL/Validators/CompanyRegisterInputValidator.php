<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class WorkspaceRegisterInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'country' => ['required'],
            'address' => ['required'],
            'seats' => ['required','integer'],
            'user.name' => ['required'],
            'user.email' => ['required','unique:users,email'],
            'user.password' => ['required','min:8'],
        ];
    }
}
