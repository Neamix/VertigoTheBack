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
        $send['view'] = 'emails.Auth.joinUsEmail';
        $send['token'] = $data['token'];
        $send['subject'] = $data['workspace']." | Invitation mail";
        $send['workspace'] = $data['workspace'];
        self::sendEmail($send);
    }

    public function subscripedSuccess($data = [])
    {
        $send['to_email'] = $data['to_email'];
        $send['name'] = $data['name'];
        $send['subject'] = 'Vertigo | '.$data['subject'];
        $send['view'] = 'emails.Invoices.success';
        $send['value_per_seats'] = $data['value_per_seat'];
        $send['seats'] = $data['seats'];
        $send['inovice_number'] = $data['inovice_number'];
        $send['credit_pm_last_four'] = $data['credit_pm_last_four'];
        $send['credit_pm_type'] = $data['credit_pm_type'];
        $send['workspace' ] = $data['workspace'];
        $send['invoiceID' ] = $data['invoiceID'];
        self::sendEmail($send);
    }   


    static function sendEmail($data)
    {
        Mail::to($data['to_email'])->send(new DefaultEmail($data));
    }
}
