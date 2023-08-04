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

Broadcast::channel('workspace.{workspace_id}', function ($user,$workspace_id) {
    if ( $user->active_workspace_id == $workspace_id ) {
        return ['user_id' => $user->id,'workspace_id' => $workspace_id];
    }

    return false;
});

Broadcast::channel('workspace-members.{workspace_id}', function ($user,$workspace_id) {
    if ( $user->active_workspace_id == $workspace_id ) {
        return ['user_id' => $user->id,'workspace_id' => $workspace_id];
    }

    return false;
});



Broadcast::channel('workspace-session-{workspace_id}', function ($workspace_id,$total_session_time) {
    return ['workspace_id' => $workspace_id,'total_session_time' => $total_session_time];
});