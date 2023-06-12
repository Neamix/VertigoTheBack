<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Request extends Model
{
    use HasFactory;

    /*** Record Change Email Request */
    public function requestEmailChange(string $email)
    {
        return Auth::user()->requests()->create([
            'type'  => 'change_email',
            'value' => $email,
            'expire_at' => Carbon::now()->addDays(1)->toDateString()
        ]);
    }

    /*** Confirm Change Email Request */
    public function confirmEmailChange($id)
    {
        $request = self::where('id',$id)->first();
        
        // Save the new Email
        Auth::user()->email = $request->value;
        Auth::user()->save();

        // Delete the used request
        $request->delete();
    }

    /*** Remove Old Requests */
    public function removeExpiredRequests()
    {
        self::where('expired_at', Carbon::now()->addDays(1)->toDateString())->delete();
    }


    // Relations 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
