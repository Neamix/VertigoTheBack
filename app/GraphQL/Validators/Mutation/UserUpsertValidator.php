<?php

namespace App\GraphQL\Validators\Mutation;

use Nuwave\Lighthouse\Validation\Validator;

final class UserUpsertValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        dd('here');
        return [
            // TODO Add your validation rules
        ];
    }
}
