<?php

namespace App\GraphQL\Validators\Mutations;

use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Validation\Validator;

final class toggleUserSuspendValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required',function ($attribute,$value,$fail){
                $user_exist = Auth::user()->activeCompany->users->where('user_id',$value)->count();

                if ( ! $user_exist ) {
                    return $fail(__('validation.error_occure_code_v103'));
                }

                if ( Auth::user()->id == $value ) {
                    return $fail(__('validation.error_occure_code_v104'));
                }
            }]
        ];
    }
}
