<?php

namespace App\GraphQL\Validators;

use App\Models\JoinRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Validation\Validator;

final class UserInvitationInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required','email','exists:users,email','exists:join_requests,email'],
            'token' => ['required',function ($attribute,$value,$fail) {
                $request = JoinRequest::where('email',$this->arg('email'))->first();
                // Check The Verification ID
                if ( ! password_verify($value,$request->token) ) {
                    return $fail(__('validation.error_occure_code_v100'));
                }
            }],
            'password' => [function($attribute,$value,$fail) {
                $user = User::where('email',$this->arg('email'))->first();

                // If User Has No Password And No Password Has Been Send From Front
                // Return Error
                if ( ! $user->password && !$value ) {
                    return $fail(__('validation.please_add_your_password'));
                }
            }]
        ];
    }
}
