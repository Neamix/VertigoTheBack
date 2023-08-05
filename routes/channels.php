<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('company.{company_id}', function ($user,$company_id) {
    if ( $user->active_company_id == $company_id ) {
        return ['user_id' => $user->id,'company_id' => $company_id];
    }

    return false;
});

Broadcast::channel('company-members.{company_id}', function ($user,$company_id) {
    if ( $user->active_company_id == $company_id ) {
        return ['user_id' => $user->id,'company_id' => $company_id];
    }

    return false;
});



Broadcast::channel('company-session-{company_id}', function ($company_id,$total_session_time) {
    return ['company_id' => $company_id,'total_session_time' => $total_session_time];
});