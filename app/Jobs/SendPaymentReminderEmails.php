<?php

namespace App\Jobs;

use App\Mail\InvoiceMail;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminderEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
       // dd("ASD");
        $invoices = DB::table('invoices')
            ->where('payment_due_date', '<', Carbon::now()->subDays(7))
            ->get();

        foreach ($invoices as $invoice) {
            // Get the file path
            $filePath = 'invoices/' . $invoice->invoice_Id . '.pdf';
            // Fetch the customer details
            $customer = Customer::findOrFail($invoice->customer_id)->first();
            // Send the email with the PDF attachment
            Mail::to($customer->email)->send(new InvoiceMail($customer, $filePath, 'sendReminder'));
        }
    }
}
