<?php

use App\Http\Controllers\RequestsController;
use App\Http\Controllers\WebhookController;
use App\Mail\DefaultEmail;
use App\Models\Company;
use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
Route::post('stripe/webhook/subscription/success',[WebhookController::class,'handleSuccessSubscription']);
Route::post('stripe/webhook/subscription/cancel',[WebhookController::class,'handleCancelSubscription']);

// Pusher Webhook
Route::post('pusher/webhook',[WebhookController::class,'handlePusherEvent']);

// Accept Invitation
Route::get('accept/invitation',[RequestsController::class,'handleInvitationRequest'])->middleware('accept.invitation');


Route::get('/test',function () {

});
