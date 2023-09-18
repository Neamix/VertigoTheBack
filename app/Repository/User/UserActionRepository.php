<?php 

namespace App\Repository\User;

use App\Events\MemberSuspend;
use App\Models\User;
use App\Repository\Status\StatusRepository;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class UserActionRepository extends BaseRepository {
    protected $statusRepository;

    public function __construct(StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    public function model()
    {
        return User::class;
    }

    /**
     * Switching member active company
     * @param company_id int 
    */

    public function switchCompany(int $comapny_id)
    {
        // Switch active company for authed memeber to sended id 
        Auth::user()->active_company_id = $comapny_id;
        $this->save();

        // Return response
        return [
            'status' => 'success',
            'user'   => Auth::user()
        ];
    }

    /** 
      * Suspend/Un suspend memeber in workspace
      * @param user_id int  
    */

    public function toggleUserSuspended($user_id) 
    {
        // Get user under action
        $user = User::where([
            'id' => $user_id
        ])->first();
         
         // Get user status
         $is_suspended = $user->companies()->where('company_id',$user->active_company_id)->first()->pivot->is_suspend;
 
         // Reverse status
         $user->companies()->updateExistingPivot($user->active_company_id,[
            'is_suspend' => ! $is_suspended,
         ]);
 
        // Send notifications
        event(new MemberSuspend([
            'user_id'     => $user->id,
            'company_id'  => Auth::user()->active_company_id,
            'event'       => ($user->is_suspend) ? 'member-suspend' : 'member-unsuspend'
        ]));

        // Return response
        return [
            'status'  => "Success",
        ];
    }

    /**
     *  Delete memeber 
     *  @param user_id int
    */

    public function deleteUser($user_id) 
    {   
        // Delete relation between selected memeber and active company of authed memeber
        Auth::user()->activeCompany->users()->detach($user_id);

        // Return response
        return [
            'status' => "Success"
        ];
    }

    public function changeStatus($status_id)
    {
        return $this->statusRepository->changeStatus($status_id);
    }

}