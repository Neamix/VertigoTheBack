<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function downloadInvoice(Company $company,$inoviceID)
    {
        return $company->generateInvoice($inoviceID);
    }
}
