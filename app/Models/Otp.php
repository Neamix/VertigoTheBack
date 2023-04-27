<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = ['otp','email','verification_id'];

    /**
     * Generate otp and assign it to sending email 
     * 
     * @warning "In case the email is already have a recorded otp this otp will be updated so the old otp will be expired"
     * @warning "To be able to use the generated otp you have to send verification id on reset"
     * 
     * @param string $email
     * 
     * @return string otp
     */

    public static function assignOtpToEmail($email)
    {
        $otp = rand(10000,99999);

        // Generate Verification ID
        $verificationID = md5(rand(1000000,9999999));

        Otp::updateOrCreate(
        [
            'email' => $email
        ],
        [
            'otp'   => $otp,
            'email' => $email,
            'verification_id' => Hash::make($verificationID)
        ]);

        return [
            'otp' => $otp,
            'verification_id' => $verificationID
        ];
    }
}
