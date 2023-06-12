<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class RequestsController extends Controller
{
    /*** Accept Invitation Token */
    public function handleInvitationRequest(Request $request)
    {
        $typeOfUser = User::renderInvitation($request->all());
        return ($typeOfUser['type'] == 'newuser') ? Redirect::to(env('FRONT_END_URL').'/verify?email='.$request->email) : Redirect::to(env('FRONT_END_URL'));
    }
}
