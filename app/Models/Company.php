<?php

namespace App\Models;

use App\Services\MailerService;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Events\WebhookReceived;

class Company extends Model
{
    use HasFactory,Billable,MailerService;

    protected $guarded = [];

    /**
     * Register new workspace company with its root user
     * 
     * Note: In case of failing in subscription the company and 
     * root user that has been created will be deleted
     * 
     * @param array $request
     * 
     * @return array 
    */

    public function register(array $request) : array
    {
        /*** Create new company */
        $company = self::create([
            'name'  => $request['name'],
            'email' => $request['email'],
            'country' => $request['country'],
            'address' => $request['address'],
            'description' => $request['description'] ?? null,
            'seats' => $request['seats'],
            'active' => false
        ]);

        /*** Generate root user */
        $rootUser = User::generateRootUser($request['user'],$company->id);
        $token =  $rootUser->login($rootUser->email, $request['user']['password'])['token'];

        /*** Try to subscripe */
        try {
            $subscripe = $company
            ->newSubscription('bronze_plan',['price_1N43m6KV5l49k0XZkSKy9RHQ'])
            ->quantity(5)
            ->create($request['billing']['id'], [
                'name'  => ($request['billing']['name'] == null) ? $request['billing']['email'] : $request['user']['name'] ,
                'email' => ($request['billing']['email'] == null) ? $request['billing']['email'] : $request['user']['email'],
                'phone' =>  $request['billing']['phone'] ?? null
            ]);

            // Send Success Email
            $this->subscripedSuccess([
                'name'      => $company->last_invoice->customer_name,
                'to_email'  => $company->last_invoice->customer_email,
                'subject'   => 'Welcome on board',
                'seats'     => $request['seats'],
                'invoiceID' => $company->last_invoice_id,
                'company'   => $company->id,
                'value_per_seat'      => 20,
                'credit_pm_last_four' => $company->pm_last_four,
                'inovice_number' => $company->invoice_number,
                'credit_pm_type' => $company->pm_type
            ]);
            
            return [
                'company' => $company,
                'user'    => $rootUser,
                'token'   => $token,
                'status'  => "Success"
            ];

        } catch (Exception $e) {
            /*** Deleted comapany and user */
            $rootUser->delete();
            $company->delete();

            return [
                'status'  => "Failed",
                'message' => $e->getMessage()
            ];
        }
    }

    // Invoices 
    public function generateInvoice($invoiceID)
    {
        /*** Generate strip invoice */
        return $this->downloadInvoice($invoiceID);
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
}
