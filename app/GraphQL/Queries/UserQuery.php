<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use App\Repository\User\UserAuthRepository;
use App\Repository\User\UserStatisticsRepository;
use Illuminate\Support\Facades\Auth;

final class UserQuery
{
    protected $user;
    protected $userStatisticsRepository;
    protected $userAuthRepository;

    public function __construct(User $user,UserStatisticsRepository $userStatisticsRepository,UserAuthRepository $userAuthRepository)
    {
        $this->user = $user;
        $this->userStatisticsRepository = $userStatisticsRepository;
        $this->userAuthRepository  = $userAuthRepository;
    }

    /*** Filter Users In Company */
    public function filterUser($_,array $args)
    {
        return User::filter($args)->where('id','!=',Auth::user()->id)->paginate($args['first']);
    }

    /*** Check Oto */
    public function checkOtp($_,array $args) 
    {
        return $this->userAuthRepository->checkOtp($args['input']['otp'],$args['input']['email']);
    }

    /*** Exporting Monitoring Sheet  */
    public function exportMonitoringSheet($_,array $args)
    {
        return  $this->user->exportMonitoringSheet($args);
    }

    public function pendingEmails($_,array $args)
    {
        return $this->user->pendingEmails($args);
    }

    /** User get statistics */
    public function getMembersReport()  {
        return $this->userStatisticsRepository->getMembersReports();
    }
}
