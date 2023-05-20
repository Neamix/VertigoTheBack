<?php

use App\Http\Controllers\Api\ApiTempController;
use App\Http\Controllers\WebhookController;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Stripe Webhook
Route::post('/test/webhook',[Company::class,'successPayment']);
Route::post('stripe/webhook',[WebhookController::class,'handleInvoicePaymentSucceeded']);