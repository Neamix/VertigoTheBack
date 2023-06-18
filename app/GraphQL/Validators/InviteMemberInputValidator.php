<?php

namespace App\GraphQL\Validators;

use App\Models\JoinRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Validation\Validator;

final class InviteMemberInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            "email" => ['required',function ($attribute,$value,$fail){
                $alreadyInvited = JoinRequest::where('email',$value)->first();

                if ( $alreadyInvited ) {
                    return $fail(__('validation.this_email_address_already_has_pending_invitation'));
                }

                $user =  User::where('email',$this->arg('email'))->first();

                if ( $user ) {
                    if ( $user->companies()->where('company_id',Auth::user()->active_company_id)->count() ) {
                        return $fail(__('validation.this_email_address_already_joined_your_workspace'));
                    }
                }
            }]
        ];
    }
}
