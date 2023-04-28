<?php

namespace App\GraphQL\Mutations;

use App\Models\Otp;
use Illuminate\Support\Facades\Auth;

final class OtpMutation
{
    protected $otp;
    public function __construct(Otp $otp)
    {
        $this->otp = $otp;
    }

    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }

    public function checkOtpByEmail($_, array $args)
    {
        return $this->otp->checkOtpByEmail($args['input']['otp'],$args['input']['verificationID'],$args['input']['type'],$args['input']['email']);
    }

    public function checkOtpByUserId($_, array $args)
    {
        return $this->otp->checkOtpByUserId($args['input']['otp'],$args['input']['verificationID'],$args['input']['type'],Auth::user()->id);
    }

}
