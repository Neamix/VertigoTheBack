<?php

namespace App\GraphQL\Validators\Mutation;

use Exception;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Validation\Validator;

final class ToggleUserSuspendedValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required',function ($attribute,$value,$fail) {
                // Get Root User Of Current Company
                $user_exist = Auth::user()->activeCompany->users->where('id',$value)->count();

                if ( ! $user_exist ) {
                    return $fail(__('validation.error_occure_code_v102'));
                }

                if ( Auth::user()->id == $value ) {
                    return $fail(__('validation.error_occure_code_v104'));
                }
            }]
        ];
    }
}
