<?php

namespace App\GraphQL\Validators;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Validation\Validator;

final class ProjectUpsertValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name'   => ['required','min:3', function ($attribute,$value,$fail) {
                $countProject  = Project::where([
                    'company_id' => Auth::user()->active_company_id,
                    'name' => $value
                ])->count();

                if ( $countProject )
                    return $fail('This Project Name Already In Use');
            }],
            'inputs' => ['required','min:1'],
        ];
    }
}
