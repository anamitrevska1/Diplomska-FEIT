<?php

namespace App\Jobs;

use App\Http\Controllers\CustomerController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;


class BillRunJob implements ShouldQueue
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
        $customers = Customer::all();

        // Initialize the CustomerController
        $customerController = new CustomerController();

        // Iterate over each customer and calculate their monthly bill
        foreach ($customers as $customer) {
            $customerController->calculateMonthlyBill($customer->id);
        }
    }
}
