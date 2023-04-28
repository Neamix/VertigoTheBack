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

    public function checkOtpByEmail(int $otp,string $verificationID,string $type,string $email)
    {
        $user   = User::where('email',$email)->first();

        return [
            'status' =>  $this->checkOtpByUserId($otp,$verificationID,$type,$user->id)
        ];
    }

    public function checkOtpByUserId(int $otp,string $verificationID,string $type,int $user_id)
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:m');
        $lastAllowedTime = Carbon::now()->subMinutes(100)->format('Y-m-d H:m');

        $otp = Otp::where([
            'verification_id' => $verificationID,
            'otp'  => $otp,
            'type' => $type,
            'user_id' => $user_id,
        ])->whereBetween('created_at',[$currentDateTime,$lastAllowedTime])->count();

        return [
            'status' => $otp ? "Success" : "Fail"
        ];
    }


}
