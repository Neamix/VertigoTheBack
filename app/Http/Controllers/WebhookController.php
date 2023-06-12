<?php

namespace App\Http\Controllers;

use App\Http\Services\PaymentService;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class WebhookController extends CashierController
{
    public function handleSuccessSubscription(PaymentService $paymentService)
    {
        $paymentService->getReleventCompany()->refreshDueDate($paymentService['object']);
    }

    public function handleCancelSubscription(PaymentService $paymentService)
    {
        $paymentService->getReleventCompany()->cancelSubscription();
    }

    public function handlePusherEvent(Request $request) {
        Log::info($request);
    }

}
