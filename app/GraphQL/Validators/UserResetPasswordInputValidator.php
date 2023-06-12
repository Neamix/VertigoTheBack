<?php

namespace App\GraphQL\Validators;

use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Nuwave\Lighthouse\Validation\Validator;

final class UserResetPasswordInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required'],
            'password' => ['required'],
            'otp' => ['required'],
            'verificationID' => ['required',function($attribute,$value,$fail) {
                if ( ! Otp::checkOtpByEmail($this->arg('otp'),$value,'password_reset',$this->arg('email')) ) {
                    return $fail(__('localization.this_otp_is_expired_or_not_exist'));
                }
             
            }]
        ];
    }
}
