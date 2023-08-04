<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function downloadInvoice(Workspace $workspace,$inoviceID)
    {
        return $workspace->generateInvoice($inoviceID);
    }
}
