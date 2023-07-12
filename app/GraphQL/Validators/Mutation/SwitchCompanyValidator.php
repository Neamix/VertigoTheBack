<?php

namespace App\GraphQL\Validators\Mutation;

use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Validation\Validator;

final class SwitchCompanyValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
           'companyid' => ['required',function ($attribute,$value,$fail) {
                // Check User if he joined That Company
                $isUserJoinCompany = Auth::user()->companies->where('id',$value)->count();

                if ( ! $isUserJoinCompany ) {
                    return $fail(__('validation.error_occure_code_v101'));
                }
           }]
        ];
    }
}
