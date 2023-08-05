<?php

namespace App\GraphQL\Validators\Mutation;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Validation\Validator;

final class DeleteUserValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'user_id' => [function ($attribute,$value,$fail) {
                // Check if user belong to active company of auth
                $user_exist = Auth::user()->activeCompany->users->where('id',$value)->count();

                if ( ! $user_exist ) {
                    return $fail(__('validation.error_occure_code_v102'));
                }
            }]
        ];
    }
}
