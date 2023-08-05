<?php

namespace App\GraphQL\Queries;

use App\Models\Company;

final class CompanyQuery
{
    public $company;

    public function __construct(Company $company)
    {
        return $this->company = $company;    
    }

    public function companyHours($_, array $args)
    {
        return $this->company->companyHoursReport();
    }
}
