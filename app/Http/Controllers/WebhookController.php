<?php

namespace App\Http\Controllers;

use App\Http\Services\PaymentService;
use App\Http\Services\PusherService;
use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class WebhookController extends CashierController
{
    public function handleSuccessSubscription(PaymentService $paymentService)
    {
        $paymentService->getReleventWorkspace()->refreshDueDate($paymentService['object']);
    }

    public function handleCancelSubscription(PaymentService $paymentService)
    {
        $paymentService->getReleventWorkspace()->cancelSubscription();
    }

    public function handlePusherEvent(PusherService $pusherService) {
        $pusherService->sessions();
    }

}
