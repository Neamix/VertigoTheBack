<?php

namespace App\Http\Controllers;

use App\Repository\User\UserInvitationRepository;
use Illuminate\Http\Request;

class RequestsController extends Controller
{
    protected $userInvitationRepository;

    public function __construct(UserInvitationRepository $userInvitationRepository)
    {
        $this->userInvitationRepository = $userInvitationRepository;    
    }

    /*** Accept Invitation Token */
    public function handleInvitationRequest(Request $request)
    {
        $typeOfUser = $this->userInvitationRepository->renderInvitation($request->all());
        return ($typeOfUser['type'] == 'newuser') ? redirect()->away('http://localhost:4000/auth/verify?email='.$request->email.'&token='.$request->token) : redirect()->away(env('FRONT_END_URL'));
    }
}
