<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = ['otp','user_id','verification_id','type'];

    public static function generateOtp($user,$type)
    {
        $otp = rand(10000,99999);

        // Generate Verification ID
        $verificationID = md5(rand(1000000,9999999));

        Otp::updateOrCreate(
        [
            'user_id' => $user->id,
            'type'  => $type
        ],
        [
            'otp'   => $otp,
            'user_id' => $user->id,
            'verification_id' => Hash::make($verificationID),
            'type' => $type
        ]);

        return [
            'otp' => $otp,
            'verification_id' => $verificationID
        ];
    }

    static function checkOtpByEmail(string $otp,string $verificationID,string $type,string $email)
    {
        $user = User::where('email',$email)->first();

        if ( $user ) {
            return [
                'status' =>  (new self)->checkOtpByUserId($otp,$verificationID,$type,$user->id)
            ];
        } else {
            return [
                'status' =>  "Failed"
            ];
        }
    }

    public function checkOtpByUserId(string $otp_code,string $verificationID,string $type,int $user_id)
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:m');
        $lastAllowedTime = Carbon::now()->subMinutes(100)->format('Y-m-d H:m');

        $otp = Otp::where([
            'otp'  => $otp_code,
            'type' => $type,
            'user_id' => $user_id,
        ])->first();

        if ( $otp ) {
            // Check Verification id
            if ( password_verify($verificationID,$otp->verification_id) ) {
                return [
                    'status' => "Success"
                ];
            }
        }

        return [
            'status' => "Fail"
        ];
    }


}
