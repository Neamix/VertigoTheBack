<?php

namespace App\GraphQL\Mutations;

use App\Models\Company;

final class CompanyMutator
{
    private $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function register($_, array $args)
    {
        return $this->company->register($args['input']);
    }
}
