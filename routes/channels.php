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
        return ['user_id' => $user->id,'company_id' => $company_id,'status_id' => $user->status_id];
    }

    return false;
});

Broadcast::channel('member.{user_id}', function ($user,$company_id) {

    if ( Auth::user()->id == $user->id )
        return ['user_id' => $user->id,'company_id' => $company_id,'status_id' => $user->status_id];
    
    return false;
});