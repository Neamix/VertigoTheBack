<?php

use App\Http\Controllers\RequestsController;
use App\Http\Controllers\WebhookController;
use App\Mail\DefaultEmail;
use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
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

Route::get('/hash',function () {
    dd(bcrypt('password'));
});

Route::get('/test',function () {
    $sessions = Session::where('company_id',1)->get(['total_session_time','start_date']);

    // Get total session
    $totalSessions = $sessions->groupBy(function($session) {
        return date('M',strtotime($session->start_date));
    });
    dd($totalSessions);
    // Get active session statistics
    $activeSessions = $sessions->where('status_id',ACTIVE)->groupBy(function($session) {
        return date('M',strtotime($session->created_at));
    })->mapWithKeys(function ($item,$key) {
        return [$key => count($item)];
    });

    // Get Busy sessions statistics
    $idleSession = $sessions->where('status_id',IDLE)->groupBy(function($session) {
        return date('M',strtotime($session->created_at));
    })->mapWithKeys(function ($item,$key) {
        return [$key => count($item)];
    });

    // Get meeting sessions statistics
    $meetingSession = $sessions->where('status_id',MEETING)->groupBy(function($session) {
        return date('M',strtotime($session->created_at));
    })->mapWithKeys(function ($item,$key) {
        return [$key => count($item)];
    });

    dd([
        'total_sessions'   => $totalSessions,
        'active_sessions'  => $activeSessions,
        'idle_sessions'    => $idleSession,
        'meeting_sessions' => $meetingSession
    ]);
});
