<?php

namespace App\Services;

use App\Mail\DefaultEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

trait MailerService
{
    public function forgetPasswordMail($data = [])
    {
        $send['name'] = $data['name'];
        $send['to_email'] = $data['to_email'];
        $send['otp'] = $data['otp'];
        $send['view'] = 'emails.Auth.forgetPasswordEmail';
        $send['subject'] = "Reset Otp Code";
        self::sendEmail($send);
    }

    public function joinUsMail($data = [])
    {
        $send['to_email'] = $data['to_email'];
        $send['view'] = 'email.Auth.joinUsEmail';
        $send['token'] = $data['token'];
        $send['subject'] = $data['company']." | Invitation mail";
        $send['company'] = $data['company'];
        self::sendEmail($send);
    }


    static function sendEmail($data)
    {
        Mail::to($data['to_email'])->send(new DefaultEmail($data));
    }
}
