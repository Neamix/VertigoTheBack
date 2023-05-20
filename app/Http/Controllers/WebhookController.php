<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class WebhookController extends CashierController
{
    public function handleInvoicePaymentSucceeded(Request $payload)
    {
        Log::info($payload);
    }
}
