<?php 

namespace App\Repository\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class UserStatisticsRepository extends BaseRepository {

    public function model()
    {
        return User::class;
    }

    public function getMembersReports()
    {
        // Get total member in workspace
        $total_members = DB::table('company_user')->where('company_id',1)->get();

        // Get total suspended members in workspace
        $total_members_suspend_count = $total_members->where('is_suspend')->count();

        // Get member report
        $total_members_monthly_report =  $total_members->groupBy(function ($member) {
            return date('M',strtotime($member->created_at));
        })->mapWithKeys(function($items,$key) {
            return [$key => count($items)];     
        })->all();


        return [
            'total_members' => $total_members->count(),
            'total_members_monthly_report' => $total_members_monthly_report,
            'total_suspended_members'  => $total_members_suspend_count
        ];
    }
}