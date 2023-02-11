<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class UserFilterInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
           'name' => ['required',function($attribute,$value,$fail){

            }]
        ];
    }
}
