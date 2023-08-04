<?php

namespace App\Models;

use App\Services\MailerService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Events\WebhookReceived;

class Workspace extends Model
{
    use HasFactory,Billable,MailerService;

    protected $guarded = [];

    /**
     * Register new workspace workspace with its root user
     * 
     * Note: In case of failing in subscription the workspace and 
     * root user that has been created will be deleted
     * 
     * @param array $request
     * 
     * @return array 
    */

    public function register(array $request) : array
    {
        /*** Create new workspace */
        $workspace = self::create([
            'name'  => $request['name'],
            'email' => $request['email'],
            'country' => $request['country'],
            'address' => $request['address'],
            'description' => $request['description'] ?? null,
            'seats' => $request['seats'],
            'active' => false
        ]);

        /*** Generate root user */
        $rootUser = User::generateRootUser($request['user'],$workspace->id);
        $token =  $rootUser->login($rootUser->email, $request['user']['password'])['token'];

        /*** Attach Owner To Workspace */
        $workspace->user_id = $rootUser->id;
        $workspace->save();
        
        /*** Try to subscripe */
        try {
            $workspace
            ->newSubscription('bronze_plan',['price_1N43m6KV5l49k0XZkSKy9RHQ'])
            ->quantity($request['seats'])
            ->create($request['billing']['id'], [
                'name'  => ($request['billing']['name'] == null) ? $request['billing']['name'] : $request['user']['name'] ,
                'email' => ($request['billing']['email'] == null) ? $request['billing']['email'] : $request['user']['email'],
                'phone' =>  $request['billing']['phone'] ?? null
            ]);

            // Send Success Email
            $this->subscripedSuccess([
                'name'      => $workspace->last_invoice->customer_name,
                'to_email'  => $workspace->last_invoice->customer_email,
                'subject'   => 'Welcome on board',
                'seats'     => $request['seats'],
                'invoiceID' => $workspace->last_invoice_id,
                'workspace'   => $workspace->id,
                'value_per_seat' => 20,
                'credit_pm_last_four' => $workspace->pm_last_four,
                'inovice_number' => $workspace->invoice_number,
                'credit_pm_type' => $workspace->pm_type
            ]);
            
            return [
                'workspace' => $workspace,
                'user'    => $rootUser,
                'token'   => $token,
                'status'  => "Success"
            ];

        } catch (Exception $e) {
            /*** Deleted comapany and user */
            $rootUser->delete();
            $workspace->delete();

            return [
                'status'  => "Failed",
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Download Invoice of given id 
     * 
     * @return Rendable
    */

    public function generateInvoice($invoiceID)
    {
        /*** Generate strip invoice */
        return $this->downloadInvoice($invoiceID);
    }

    /**
     * Add new due date for workspace after paying subscription 
     * 
     * @return void
    */

    public function refreshDueDate()
    {
        /*** Get next due date (fixed 30 days ) and save it in database */
        $workspace_next_due_date = Carbon::now()->addDays(30)->toDateTimeString();
        $this->next_due_date  = date('Y-m-d',strtotime($workspace_next_due_date));
        $this->save();
    }

    public function cancelSubscription()
    {

    }

    /*** Get Total Hours Of Agents During This Year */
    public function workspaceHoursReport() 
    {
        $workspace_total_hours = Workspace::find(1)->sessions()->get();

        $total_hours = $workspace_total_hours->groupby(function ($session) {
            return Carbon::parse($session->created_at)->format('m');
        });

        $total_hours_report = $workspace_total_hours->groupby(function ($session) {
            return Carbon::parse($session->created_at)->format('m');
        })->mapWithKeys(function ($month_report) {
            return ['time' => $month_report->sum('total_session_time')];
        });

        $active_hours_report = $workspace_total_hours->where('status_id',ACTIVE)->groupby(function ($session) {
            return Carbon::parse($session->created_at)->format('m');
        })->map(function ($month_report) {
            return $month_report->sum('total_session_time');
        })->all();

        $idle_hours_report = $workspace_total_hours->where('status_id',IDLE)->groupby(function ($session) {
            return Carbon::parse($session->created_at)->format('m');
        })->map(function ($month_report) {
            return $month_report->sum('total_session_time');
        })->all();

        $meeting_hours_report = $workspace_total_hours->where('status_id',MEETING)->groupby(function ($session) {
            return Carbon::parse($session->created_at)->format('m');
        })->map(function ($month_report) {
            return $month_report->sum('total_session_time');
        })->all();

        return [
            'time' => 100,
        ];
    }

    // Attributes
    public function getLastInvoiceAttribute()
    {
        return $this->invoices()->last()->asStripeInvoice();
    }

    public function getLastInvoiceIdAttribute()
    {
        return $this->invoices()->last()->asStripeInvoice()->id;
    }

    public function getInvoiceNumbertAttribute()
    {
        return $this->invoices()->last()->asStripeInvoice()->number;
    }

    // Relations
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
