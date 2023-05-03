<?php

namespace App\GraphQL\Validators;

use App\Models\EmailRequest;
use App\Models\User;
use Nuwave\Lighthouse\Validation\Validator;

final class UserProfileInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            "email" => ['email',function ($attribute,$value,$fail){
                // Check if any user requested to own this email 
                $emailRequestCount = EmailRequest::where([
                    'email' => $value
                ])->count();
                
                // Check if any User already have this email
                $usersWithEmailCount = User::where([
                    'email' => $value
                ])->count();

                if($emailRequestCount || $usersWithEmailCount) {
                    return $fail(__('localization.someone_have_this_email_or_have_request_to_own_it'));
                }
            }]
        ];
    }
}
