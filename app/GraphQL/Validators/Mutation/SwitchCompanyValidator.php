<?php

namespace App\GraphQL\Validators\Mutation;

use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Validation\Validator;

final class SwitchWorkspaceValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
           'workspaceid' => ['required',function ($attribute,$value,$fail) {
                // Check User if he joined That Workspace
                $isUserJoinWorkspace = Auth::user()->companies->where('id',$value)->count();

                if ( ! $isUserJoinWorkspace ) {
                    return $fail(__('validation.error_occure_code_v101'));
                }
           }]
        ];
    }
}
