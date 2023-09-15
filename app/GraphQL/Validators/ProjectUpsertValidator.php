<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class ProjectUpsertValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name'   => ['required','min:3'],
            'inputs' => ['required','min:1'],
            'accessableMembers' => ['required','min:1'] 
        ];
    }
}
