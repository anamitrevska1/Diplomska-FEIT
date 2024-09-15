<?php

namespace App\Http\Controllers;
use App\Http\Controllers\InvoiceController;
use App\Models\Customer;
use App\Models\customer_discount;
use App\Models\Discount;
use App\Models\Service;
use App\Models\customer_service;
use App\Models\service_charge_map;
use App\Models\Invoices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::query()->orderBy('id', 'desc')->paginate();

        return view('customer.index',['customer'=>$customers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'FirstName' => 'required','string','max:255',
            'LastName' => 'required','string','max:255',
            'CompanyName'=>'string',
            'CustomerType'=>'required|string',
            'email'=>'required|email',
            'address'=>'required|string',
            'city'=>'required|string',
            'zip'=>'required|string',
            'PhoneNumber'=>'required',
            'BillPeriod' =>'required'
        ]);
        $created_by=Auth::user()->id;
        $customer = new Customer();
        $customer->created_by=$created_by;
        $customer->customer_type=request('CustomerType');
        $customer->first_name=request('FirstName');
        $customer->last_name=request('LastName');
        $customer->company_name=request('CompanyName');
        $customer->email=request('email');
        $customer->phone=request('PhoneNumber');
        $customer->address=request('address');
        $customer->city=request('city');
        $customer->zip=request('zip');
        $customer->bill_period=request('BillPeriod');
        $customer->prev_cutoff_date=Carbon::now()->startOfMonth();
        $customer->save();

        return redirect('/customer/'.$customer->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $created_by=Auth::user()->id;
        $customerid=$customer->id;
        $billingDate =  null;
        $serviceCustomer = DB::table('customer_services')
            ->join('services', 'services.id', '=', 'customer_services.service_id')
            ->where('customer_services.customer_id',$customer->id)
            ->whereNull('customer_services.deleted_at')
            ->select('customer_services.*','services.name as service_name','services.type as service_type','services.service_charge as service_charge')
            ->paginate(10);
        $serviceList = DB::table('services')
            ->where('created_by',$created_by)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();
        $discountList = DB::table('discounts')
            ->where('created_by',$created_by)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        $discountCustomerList = DB::table('customer_discounts')
            ->join('discounts', 'discounts.id', '=', 'customer_discounts.discount_id')
            ->leftJoin('customer_services', 'customer_discounts.service_id', '=', 'customer_services.id')
            ->leftJoin('services', 'services.id', '=', 'customer_services.service_id')
            ->where('customer_discounts.customer_id',$customer->id)
            ->whereNull('customer_discounts.deleted_at')
            ->select('customer_discounts.*','services.name as discount_on_service_name','discounts.name as discount_name','discounts.discount_amount as discount_amount','discounts.discount_percentage as discount_percentage')
            ->paginate(10);

        $invoiceList = DB::table('invoices')
            ->where('customer_id',$customerid)
            ->where('type','=', 1)
            ->orderBy('created_at')
            ->paginate(10);

        // dd($invoiceList);
        // $billingDetails = $this->calculateMonthlyBill($customerid,$billingDate);

       // $billingDetails = $this-> calculateProformaBill($customerid,$billingDate);

        $openBillAmount=$this->showOpenBillAmount($customerid,$billingDate);

       // dd($billingDetails);
        return view('customer.show',['customer'=>$customer,'serviceCustomer'=>$serviceCustomer,'serviceList'=>$serviceList,'discountList'=>$discountList,'discountCustomerList'=>$discountCustomerList,'invoiceList'=>$invoiceList,'openBillAmount'=>$openBillAmount]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customer.edit',['customer'=>$customer]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $request->validate([
            'FirstName' => 'required','string','max:255',
            'LastName' => 'required','string','max:255',
            'CompanyName'=>'string',
            'CustomerType'=>'required|string',
            'email'=>'required|email',
            'address'=>'required|string',
            'city'=>'required|string',
            'zip'=>'required|string',
            'PhoneNumber'=>'required',
            'BillPeriod' =>'required'
        ]);
        $customer=Customer::findOrFail($id);
        $customer->first_name=request()->input('FirstName');
        $customer->last_name=request()->input('LastName');
        $customer->company_name=request()->input('CompanyName');
        $customer->customer_type=request()->input('CustomerType');
        $customer->email=request()->input('email');
        $customer->address=request()->input('address');
        $customer->city=request()->input('city');
        $customer->zip=request()->input('zip');
        $customer->phone=request()->input('PhoneNumber');
        $customer->bill_period=request()->input('BillPeriod');
        $customer->save();
        return redirect('/customer/'.$customer->id);

   }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }

    public function suppress($id)
    {
        $isSuppressed=Customer::findOrFail($id)->no_bill;
        $customer=Customer::findOrFail($id);
        if($isSuppressed===0){
            $isSuppressed=1;
        }
        else {
            $isSuppressed=0;
        }

        $customer->no_bill=$isSuppressed;
        $customer->save();
        return redirect('/customer/'.$customer->id);
    }


    public function customerServiceAdd(Request $request)
    {

        $selectedService = Service::findorfail($request->input('selectService'));
        $created_by=Auth::user()->id;
        $customerId = $request->input('addServiceCustomerId');

        // Check if the service type is "NRC"
        if ($selectedService->type == 'NRC') {
            // Set start date to the first day of the month
            $serviceStartDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            // Set end date to the last day of the month
            $serviceEndDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        } else {

            $validated = $request->validate([
                'serviceStartDate' => 'required|date_format:m/d/Y',
                'serviceEndDate' => 'required|date_format:m/d/Y',
            ]);
            // Use the provided start and end dates
            $serviceStartDate = Carbon::createFromFormat('m/d/Y', $validated['serviceStartDate'])->format('Y-m-d');
            $serviceEndDate = Carbon::createFromFormat('m/d/Y', $validated['serviceEndDate'])->format('Y-m-d');
        }

        $customerservice = new customer_service();
        $customerservice->user_id=$created_by;
        $customerservice->customer_id = $customerId;
        $customerservice->service_id=$selectedService->id;
        $customerservice->active_Date=$serviceStartDate;
        $customerservice->end_Date=$serviceEndDate;
        $customerservice->save();

        $servicecharge = new service_charge_map();
        $servicecharge->customer_id = $customerId;
        $servicecharge->service_id = $request->input('selectService');
        $servicecharge->active_Date=$serviceStartDate;
        $servicecharge->end_Date=$serviceEndDate;
        $servicecharge->service_type=$selectedService->type;
        $servicecharge->customer_service_map_id=$customerservice->id;
        $servicecharge->save();
        return redirect('/customer/'.$customerId);
    }

public function deactivateService($id)
{
    $todayDate = Carbon::now()->format('Y-m-d');
    $customerService=customer_service::findOrFail($id);
    $serviceType=service::findOrfail($customerService->service_id)->type;
    $prev_cutoff_date=customer::findOrfail($customerService->customer_id)->prev_cutoff_date;
    if($serviceType=='RC'){
        if ($todayDate<$customerService->active_Date && $todayDate<$customerService->end_date ){
            $customerService->end_date=$customerService->active_Date;
            $customerService->save();
            DB::table('service_charge_maps')
                ->updateOrInsert(
                    ['customer_service_map_id' => $id],
                    ['end_date' => $customerService->end_date]
                );
        }
        else if ($todayDate>$customerService->active_Date && $todayDate<$customerService->end_date){
        $customerService->end_date=$todayDate;
        $customerService->save();
            DB::table('service_charge_maps')
                ->updateOrInsert(
                    ['customer_service_map_id' => $id],
                    ['end_date' => $customerService->end_date]
                );
        }
    }
    else if($serviceType=='NRC' && $customerService->end_date>$prev_cutoff_date){
        $customerService->delete();
    }

    return redirect('/customer/'.$customerService->customer_id);
}


    public function customerDiscountAdd(Request $request)
    {

        $validated = $request->validate([
            'discountStartDate' => 'required|date_format:m/d/Y',
            'discountEndDate' => 'required|date_format:m/d/Y',
        ]);

        $created_by=Auth::user()->id;
        $customerId = $request->input('addDiscountCustomerId');
        $discountStartDate = Carbon::createFromFormat('m/d/Y', $validated['discountStartDate'])->format('Y-m-d');
        $discountEndDate = Carbon::createFromFormat('m/d/Y', $validated['discountEndDate'])->format('Y-m-d');

        $customerdiscount = new customer_discount();
        $customerdiscount->user_id=$created_by;
        $customerdiscount->customer_id = $customerId;
        $customerdiscount->service_id = $request->has('isCheckedctiveService') ? $request->input('selectActiveService') : null; /* customer_services id */
        $customerdiscount->discount_id = $request->input('selectDiscount');
        $customerdiscount->active_Date = $discountStartDate;
        $customerdiscount->end_Date = $discountEndDate;

        $customerdiscount->save();
        return redirect('/customer/'.$customerId);
    }


    public function deactivateDiscount($id)
    {

        $customerDiscount=customer_discount::findorfail($id);
        $customerDiscount->delete();

        return redirect('/customer/'.$customerDiscount->customer_id);
    }






    function calculateMonthlyBill($customerId,Carbon $billingDate = null)
    {
        $totalPrice = 0.0;
        $serviceDetails = [];
        $discountDetails = [];
        $totalDiscount = 0;

        $customer = DB::table('customers')
            ->where('id', $customerId)
            ->first();
        // Check if customer exists
        if (!$customer) {
            return [
                'message' => 'Customer not found.',
            ];
        }

        // Check if billing should be skipped
        if ($customer->no_bill == 1) {
            return [
                'message' => 'Customer is suppressed',
            ];
        }

        $prevCutoffDate = Carbon::parse($customer->prev_cutoff_date);

        // Take today's date
        $billingDate = Carbon::now();

        if ($prevCutoffDate->greaterThanOrEqualTo($billingDate->startOfMonth())) {
            // If prev_cutoff_Date is not before the current month, return or skip billing
            return [
                'message' => 'Billing not required for this customer.',
            ];
        }

        $servicesCustomer = DB::table('customer_services')
            ->join('services', 'services.id', '=', 'customer_services.service_id')
            ->where('customer_services.customer_id',$customerId)
            ->whereNull('customer_services.deleted_at')
            ->select('customer_services.*','services.name as service_name','services.type as service_type','services.service_charge as service_charge')
            ->get();

        $discountCustomerList = DB::table('customer_discounts')
            ->join('discounts', 'discounts.id', '=', 'customer_discounts.discount_id')
            ->leftjoin('customer_services', 'customer_discounts.service_id', '=', 'customer_services.id')
            ->leftjoin('services', 'services.id', '=', 'customer_services.service_id')
            ->where('customer_discounts.customer_id',$customerId)
            ->whereNull('customer_discounts.deleted_at')
            ->select('customer_discounts.*','services.name as discount_on_service_name','discounts.name as discount_name','discounts.discount_amount as discount_amount','discounts.discount_percentage as discount_percentage')
            ->get();
       // dd($discountCustomerList);
        // Determine the billing period

        $billingStartDate = $prevCutoffDate->copy()->startOfMonth();
        $billingEndDate = $prevCutoffDate->copy()->endOfMonth();
     //   $payment_due_date = $billingDate->copy()->endOfMonth();

        // Determine payment due date based on the billing date
        if ($billingDate->day > 14) {
            $payment_due_date = $billingDate->copy()->addMonth()->endOfMonth();
        } else {
            $payment_due_date = $billingDate->copy()->endOfMonth();
        }

        foreach ($servicesCustomer as $service) {

            // Convert dates to Carbon instances
            $activeDate =  Carbon::parse($service->active_Date);
            $inactiveDate = Carbon::parse($service->end_date);

            // Check if the service was active during the billing period
            if ($billingEndDate < $activeDate || $billingStartDate > $inactiveDate) {
                continue; // Skip services that were not active during the billing period
            }

            // Determine the start and end date of the service for billing within the month
            $billingStart = max($activeDate, $billingStartDate);
            $billingEnd = min($inactiveDate, $billingEndDate);


            // Calculate the number of days the service was active in the billing month
            $daysInMonth = $prevCutoffDate->daysInMonth;
            $activeDaysInMonth = $billingStart->diffInDays($billingEnd) + 1;

       //     dd($billingStart,$billingEnd,$daysInMonth,$activeDaysInMonth);

            // Pro-rate the monthly price based on the active days
            $pricePerMonth = $service->service_charge;
            $proratedPrice = ($activeDaysInMonth / $daysInMonth) * $pricePerMonth;

            // Calculate the  discount
            $serviceDiscount = 0;
            foreach ($discountCustomerList as $discount) {
               // dd($discountCustomerList);
                if (isset($discount->service_id) && $discount->service_id == $service->id) {
                    // Convert dates to Carbon instances
                    $discountStartDate = Carbon::parse($discount->active_Date);
                    $discountEndDate = Carbon::parse($discount->end_date);
                   // dd($discountStartDate, $discountEndDate);
                    // Check if the discount is applicable during the billing period
                    if ($billingEndDate < $discountStartDate || $billingStartDate > $discountEndDate) {
                        continue;
                    }

                    $discountStart = max($discountStartDate, $billingStartDate);
                    $discountEnd = min($discountEndDate, $billingEndDate);
                    $discountDays = $discountStart->diffInDays($discountEnd) + 1;
                  //  dd($discountStart, $discountEnd, $discountDays);
                    if (isset($discount->discount_percentage)) {
                        $serviceDiscount += $proratedPrice * ($discount->discount_percentage / 100);
                    } else { // fixed amount
                        $serviceDiscount+= $discount->discount_amount;
                       // dd($serviceDiscount);
                    }
                    $discountDetails[] = [
                        'user_id' => $discount->user_id,
                        'service_id' => $discount->service_id,
                        'customer_id' => $discount->customer_id,
                        'discount_name' => $discount->discount_name,
                        'from_date' => $discount->active_Date,
                        'to_date' => $discount->end_date,
                        'discount_amount' => $discount->discount_amount,
                        'discount_percentage' => $discount->discount_percentage,
                        'discount_on_service_name' => $discount->discount_on_service_name
                    ];
                }



            }
               // dd($discountDetails);
            // Subtract the discount from the prorated price
            $totalServicePrice = $proratedPrice - $serviceDiscount;
            //If below 0 , set it to 0

            $totalServicePrice = max($totalServicePrice, 0);
            // Add to the total price
            $totalPrice += $totalServicePrice;

            // Append detailed service price info
            $serviceDetails[] = [
                'user_id' => Auth::user()->id,
                'service_id' => $service->id,
                'customer_id' => $customerId,
                'service_name' => $service->service_name,
                'active_days_in_month' => $activeDaysInMonth,
                'from_date' => $billingStart,
                'to_date' => $billingEnd,
                'price_per_month' => $pricePerMonth,
                'prorated_price' => $proratedPrice,
                'service_discount' => $serviceDiscount,
                'total_service_price' => $totalServicePrice
            ];

        }

        // Apply discount on the total price if no service_id is set
        $totalDiscount = 0;
        foreach ($discountCustomerList as $discount) {
            if (!isset($discount->service_id)) {
                if (isset($discount->discount_percentage)) {
                    $totalDiscount += $totalPrice * ($discount->discount_percentage / 100);
                } else { // fixed amount
                    $totalDiscount += $discount->discount_amount;
                }

                $discountDetails[] = [
                    'user_id' => $discount->user_id,
                    'service_id' => null,
                    'customer_id' => $discount->customer_id,
                    'discount_name' => $discount->discount_name,
                    'from_date' => $discount->active_Date,
                    'to_date' => $discount->end_date,
                    'discount_amount' => $discount->discount_amount,
                    'discount_percentage' => $discount->discount_percentage,
                    'discount_on_service_name' => 'Total Price Discount'
                ];
            }
        }

        $totalPrice = max($totalPrice - $totalDiscount, 0);
        $totalPrice=round($totalPrice);
        $invoice = Invoices::create([
            'customer_id' => $customerId,
            'type' => 1,
            'user_id' => Auth::user()->id,
            'from_date' => $billingStartDate,
            'to_date' => $billingEndDate,
            'payment_due_date' => $payment_due_date,
            'total_amount' => $totalPrice,
        ]);

        // Insert service details into invoice_service_items
        foreach ($serviceDetails as $serviceDetail) {
            DB::table('invoice_service_items')->insert([
                'invoice_id' => $invoice->invoice_id,
                'user_id' => $serviceDetail['user_id'],
                'customer_id' => $serviceDetail['customer_id'],
                'service_id' => $serviceDetail['service_id'],
                'service_name' => $serviceDetail['service_name'],
                'from_date' => $serviceDetail['from_date'],
                'to_date' => $serviceDetail['to_date'],
                'active_days_in_month' => $serviceDetail['active_days_in_month'],
                'price_per_month' => $serviceDetail['price_per_month'],
                'prorated_price' => $serviceDetail['prorated_price'],
                'service_discount' => $serviceDetail['service_discount'],
                'total_service_price' => $serviceDetail['total_service_price'],
            ]);
        }

        // Insert discount details into invoice_discount_items
        foreach ($discountDetails as $discountDetail) {
            DB::table('invoice_discount_items')->insert([
                'invoice_id' => $invoice->invoice_id,
                'user_id' => $discountDetail['user_id'],
                'customer_id' => $discountDetail['customer_id'],
                'service_id' => $discountDetail['service_id'] ?? null,
                'discount_name' => $discountDetail['discount_name'],
                'from_date' => $discountDetail['from_date'],
                'to_date' => $discountDetail['to_date'],
                'discount_amount' => $discountDetail['discount_amount'],
                'discount_percentage' => $discountDetail['discount_percentage'],
                'discount_on_service_name' => $discountDetail['discount_on_service_name'],
            ]);
        }


        // Update the prev_cutoff_Date to one month after its original value -- UNCOMMENT AT THE END

        DB::table('customers')
        ->where('id', $customerId)
        ->update(['prev_cutoff_Date' => $prevCutoffDate->addMonth()]);

        $invoiceController = new InvoiceController();
        return $invoiceController->generateInvoicePdf($invoice->invoice_id,$invoice->type);
    }



    function calculateProformaBill($customerId,Carbon $billingDate = null)
    {
        $totalPrice = 0.0;
        $serviceDetails = [];
        $discountDetails = [];
        $totalDiscount = 0;

        $customer = DB::table('customers')
            ->where('id', $customerId)
            ->first();

       $prevCutoffDate = Carbon::parse($customer->prev_cutoff_date);

        // Take today's date
        $billingDate = Carbon::now();

        $servicesCustomer = DB::table('customer_services')
            ->join('services', 'services.id', '=', 'customer_services.service_id')
            ->where('customer_services.customer_id',$customerId)
            ->whereNull('customer_services.deleted_at')
            ->select('customer_services.*','services.name as service_name','services.type as service_type','services.service_charge as service_charge')
            ->get();
        $discountCustomerList = DB::table('customer_discounts')
            ->join('discounts', 'discounts.id', '=', 'customer_discounts.discount_id')
            ->leftjoin('customer_services', 'customer_discounts.service_id', '=', 'customer_services.id')
            ->leftjoin('services', 'services.id', '=', 'customer_services.service_id')
            ->where('customer_discounts.customer_id',$customerId)
            ->whereNull('customer_discounts.deleted_at')
            ->select('customer_discounts.*','services.name as discount_on_service_name','discounts.name as discount_name','discounts.discount_amount as discount_amount','discounts.discount_percentage as discount_percentage')
            ->get();

      //dd($discountCustomerList);

        // Determine the billing period for the previous month

        $billingStartDate = $prevCutoffDate->copy()->startOfMonth();
        $billingEndDate = $prevCutoffDate->copy()->endOfMonth();
        $payment_due_date = $prevCutoffDate->copy()->endOfMonth();


        foreach ($servicesCustomer as $service) {

            // Convert dates to Carbon instances
            $activeDate =  Carbon::parse($service->active_Date);
            $inactiveDate = Carbon::parse($service->end_date);

            // Check if the service was active during the billing period
            if ($billingEndDate < $activeDate || $billingStartDate > $inactiveDate) {
                continue; // Skip services that were not active during the billing period
            }


            // Determine the start and end date of the service for billing within the month
            $billingStart = max($activeDate, $billingStartDate);
            $billingEnd = min($inactiveDate, $billingEndDate);


            // Calculate the number of days the service was active in the billing month
            $daysInMonth = $prevCutoffDate->daysInMonth;
            $activeDaysInMonth = $billingStart->diffInDays($billingEnd) + 1;

            //     dd($billingStart,$billingEnd,$daysInMonth,$activeDaysInMonth);

            // Pro-rate the monthly price based on the active days
            $pricePerMonth = $service->service_charge;
            $proratedPrice = ($activeDaysInMonth / $daysInMonth) * $pricePerMonth;

            // dd($pricePerMonth, $proratedPrice);

            // Calculate the prorated discount
            $serviceDiscount = 0;
            //dd($billingEndDate,$billingStartDate);
            foreach ($discountCustomerList as $discount) {
                if (isset($discount->service_id) && $discount->service_id == $service->id) {
                    // Convert dates to Carbon instances
                    $discountStartDate = Carbon::parse($discount->active_Date);
                    $discountEndDate = Carbon::parse($discount->end_date);
                    // dd($discountStartDate, $discountEndDate);
                    // Check if the discount is applicable during the billing period
                    if ($billingEndDate < $discountStartDate || $billingStartDate > $discountEndDate) {
                        continue;
                    }

                    $discountStart = max($discountStartDate, $billingStartDate);
                    $discountEnd = min($discountEndDate, $billingEndDate);
                    $discountDays = $discountStart->diffInDays($discountEnd) + 1;
                    //  dd($discountStart, $discountEnd, $discountDays);
                    if (isset($discount->discount_percentage)) {
                        $serviceDiscount += $proratedPrice * ($discount->discount_percentage / 100);
                    } else { // fixed amount
                        $serviceDiscount+= $discount->discount_amount;
                        // dd($serviceDiscount);
                    }

                    $discountDetails[] = [
                        'user_id' => $discount->user_id,
                        'service_id' => $discount->service_id,
                        'customer_id' => $discount->customer_id,
                        'discount_name' => $discount->discount_name,
                        'from_date' => $discount->active_Date,
                        'to_date' => $discount->end_date,
                        'discount_amount' => $discount->discount_amount,
                        'discount_percentage' => $discount->discount_percentage,
                        'discount_on_service_name' => $discount->discount_on_service_name
                    ];
                }


            }
         //dd($discountDetails);

            // Subtract the discount from the prorated price
            $totalServicePrice = $proratedPrice - $serviceDiscount;
            $totalServicePrice = max($totalServicePrice, 0);
            // Add to the total price
            $totalPrice += $totalServicePrice;
            // Append detailed service price info
            $serviceDetails[] = [
                'user_id' => Auth::user()->id,
                'service_id' => $service->id,
                'customer_id' => $customerId,
                'service_name' => $service->service_name,
                'active_days_in_month' => $activeDaysInMonth,
                'from_date' => $billingStart,
                'to_date' => $billingEnd,
                'price_per_month' => $pricePerMonth,
                'prorated_price' => $proratedPrice,
                'service_discount' => $serviceDiscount,
                'total_service_price' => $totalServicePrice
            ];

             //dd($discountDetails);
        }
        // Apply discount on the total price if no service_id is set
        $totalDiscount = 0;
        foreach ($discountCustomerList as $discount) {
            if (!isset($discount->service_id)) {
                if (isset($discount->discount_percentage)) {
                    $totalDiscount += $totalPrice * ($discount->discount_percentage / 100);
                } else { // fixed amount
                    $totalDiscount += $discount->discount_amount;
                }

                $discountDetails[] = [
                    'user_id' => $discount->user_id,
                    'customer_id' => $discount->customer_id,
                    'discount_name' => $discount->discount_name,
                    'from_date' => $discount->active_Date,
                    'to_date' => $discount->end_date,
                    'discount_amount' => $discount->discount_amount,
                    'discount_percentage' => $discount->discount_percentage,
                    'discount_on_service_name' => 'Total Price Discount'
                ];
            }
        }
        //dd($discountDetails);
        $totalPrice = max($totalPrice - $totalDiscount, 0);
        $totalPrice=round($totalPrice);
        $invoice = Invoices::create([
            'customer_id' => $customerId,
            'type' => 4,
            'isPaid' => 0,
            'user_id' => Auth::user()->id,
            'from_date' => $billingStartDate,
            'to_date' => $billingEndDate,
            'payment_due_date' => $payment_due_date,
            'total_amount' => $totalPrice,

        ]);

        // Insert service details into invoice_service_items
       // dd($serviceDetails);
        foreach ($serviceDetails as $serviceDetail) {
            DB::table('invoice_service_items')->insert([
                'invoice_id' => $invoice->invoice_id,
                'user_id' => $serviceDetail['user_id'],
                'customer_id' => $serviceDetail['customer_id'],
                'service_id' => $serviceDetail['service_id'],
                'service_name' => $serviceDetail['service_name'],
                'from_date' => $serviceDetail['from_date'],
                'to_date' => $serviceDetail['to_date'],
                'active_days_in_month' => $serviceDetail['active_days_in_month'],
                'price_per_month' => $serviceDetail['price_per_month'],
                'prorated_price' => $serviceDetail['prorated_price'],
                'service_discount' => $serviceDetail['service_discount'],
                'total_service_price' => $serviceDetail['total_service_price'],
            ]);
        }

        // Insert discount details into invoice_discount_items
     // dd($discountDetails);
        foreach ($discountDetails as $discountDetail) {
            DB::table('invoice_discount_items')->insert([
                'invoice_id' => $invoice->invoice_id,
                'user_id' => $discountDetail['user_id'],
                'customer_id' => $discountDetail['customer_id'],
                'service_id' => $discountDetail['service_id'] ?? null,
                'discount_name' => $discountDetail['discount_name'],
                'from_date' => $discountDetail['from_date'],
                'to_date' => $discountDetail['to_date'],
                'discount_amount' => $discountDetail['discount_amount'],
                'discount_percentage' => $discountDetail['discount_percentage'],
                'discount_on_service_name' => $discountDetail['discount_on_service_name'],
            ]);
        }


        $invoiceController = new InvoiceController();
        return $invoiceController->generateInvoicePdf($invoice->invoice_id,$invoice->type);

    }


    function showOpenBillAmount($customerId,Carbon $billingDate = null)
    {
        $totalPrice = 0.0;
        $serviceDetails = [];
        $discountDetails = [];
        $totalDiscount = 0;

        $customer = DB::table('customers')
            ->where('id', $customerId)
            ->first();

        $prevCutoffDate = Carbon::parse($customer->prev_cutoff_date);

        // Take today's date
        $billingDate = Carbon::now();

        $servicesCustomer = DB::table('customer_services')
            ->join('services', 'services.id', '=', 'customer_services.service_id')
            ->where('customer_services.customer_id',$customerId)
            ->whereNull('customer_services.deleted_at')
            ->select('customer_services.*','services.name as service_name','services.type as service_type','services.service_charge as service_charge')
            ->get();
        $discountCustomerList = DB::table('customer_discounts')
            ->join('discounts', 'discounts.id', '=', 'customer_discounts.discount_id')
            ->leftjoin('customer_services', 'customer_discounts.service_id', '=', 'customer_services.id')
            ->leftjoin('services', 'services.id', '=', 'customer_services.service_id')
            ->where('customer_discounts.customer_id',$customerId)
            ->whereNull('customer_discounts.deleted_at')
            ->select('customer_discounts.*','services.name as discount_on_service_name','discounts.name as discount_name','discounts.discount_amount as discount_amount','discounts.discount_percentage as discount_percentage')
            ->get();

        // Determine the billing period for the previous month

        $billingStartDate = $prevCutoffDate->copy()->startOfMonth();
        $billingEndDate = $prevCutoffDate->copy()->endOfMonth();
        $payment_due_date = $prevCutoffDate->copy()->endOfMonth();
        foreach ($servicesCustomer as $service) {

            // Convert dates to Carbon instances
            $activeDate =  Carbon::parse($service->active_Date);
            $inactiveDate = Carbon::parse($service->end_date);

            // Check if the service was active during the billing period
            if ($billingEndDate < $activeDate || $billingStartDate > $inactiveDate) {
                continue; // Skip services that were not active during the billing period
            }


            // Determine the start and end date of the service for billing within the month
            $billingStart = max($activeDate, $billingStartDate);
            $billingEnd = min($inactiveDate, $billingEndDate);


            // Calculate the number of days the service was active in the billing month
            $daysInMonth = $prevCutoffDate->daysInMonth;
            $activeDaysInMonth = $billingStart->diffInDays($billingEnd) + 1;

            //     dd($billingStart,$billingEnd,$daysInMonth,$activeDaysInMonth);

            // Pro-rate the monthly price based on the active days
            $pricePerMonth = $service->service_charge;
            $proratedPrice = ($activeDaysInMonth / $daysInMonth) * $pricePerMonth;

            // dd($pricePerMonth, $proratedPrice);

            // Calculate the prorated discount
            $serviceDiscount = 0;
            foreach ($discountCustomerList as $discount) {
                //dd($discountCustomerList);
                if (isset($discount->service_id) && $discount->service_id == $service->id) {
                    // Convert dates to Carbon instances
                    $discountStartDate = Carbon::parse($discount->active_Date);
                    $discountEndDate = Carbon::parse($discount->end_date);
                    // dd($discountStartDate, $discountEndDate);
                    // Check if the discount is applicable during the billing period
                    if ($billingEndDate < $discountStartDate || $billingStartDate > $discountEndDate) {
                        continue;
                    }

                    $discountStart = max($discountStartDate, $billingStartDate);
                    $discountEnd = min($discountEndDate, $billingEndDate);
                    $discountDays = $discountStart->diffInDays($discountEnd) + 1;
                    //  dd($discountStart, $discountEnd, $discountDays);
                    if (isset($discount->discount_percentage)) {
                        $serviceDiscount += $proratedPrice * ($discount->discount_percentage / 100);
                    } else { // fixed amount
                        $serviceDiscount+= $discount->discount_amount;
                        // dd($serviceDiscount);
                    }
                }

                $discountDetails[] = [
                    'user_id' => $discount->user_id,
                    'service_id' => $discount->service_id,
                    'customer_id' => $discount->customer_id,
                    'discount_name' => $discount->discount_name,
                    'from_date' => $discount->active_Date,
                    'to_date' => $discount->end_date,
                    'discount_amount' => $discount->discount_amount,
                    'discount_percentage' => $discount->discount_percentage,
                    'discount_on_service_name' => $discount->discount_on_service_name
                ];
                //  dd($discountDetails);
            }

            // Subtract the discount from the prorated price
            $totalServicePrice = $proratedPrice - $serviceDiscount;
            $totalServicePrice = max($totalServicePrice, 0);
            // Add to the total price
            $totalPrice += $totalServicePrice;
            // Append detailed service price info
            $serviceDetails[] = [
                'user_id' => Auth::user()->id,
                'service_id' => $service->id,
                'customer_id' => $customerId,
                'service_name' => $service->service_name,
                'active_days_in_month' => $activeDaysInMonth,
                'from_date' => $billingStart,
                'to_date' => $billingEnd,
                'price_per_month' => $pricePerMonth,
                'prorated_price' => $proratedPrice,
                'service_discount' => $serviceDiscount,
                'total_service_price' => $totalServicePrice
            ];

        }

        // Apply discount on the total price if no service_id is set
        $totalDiscount = 0;
        foreach ($discountCustomerList as $discount) {
            if (!isset($discount->service_id)) {
                if (isset($discount->discount_percentage)) {
                    $totalDiscount += $totalPrice * ($discount->discount_percentage / 100);
                } else { // fixed amount
                    $totalDiscount += $discount->discount_amount;
                }

                $discountDetails[] = [
                    'user_id' => $discount->user_id,
                    'customer_id' => $discount->customer_id,
                    'discount_name' => $discount->discount_name,
                    'from_date' => $discount->active_Date,
                    'to_date' => $discount->end_date,
                    'discount_amount' => $discount->discount_amount,
                    'discount_percentage' => $discount->discount_percentage,
                    'discount_on_service_name' => 'Total Price Discount'
                ];
            }
        }
        //dd($discountDetails);
        $totalPrice = max($totalPrice - $totalDiscount, 0);
        $totalPrice=round($totalPrice);

        return [
       'total_price' => $totalPrice,
        ];
    }

}
