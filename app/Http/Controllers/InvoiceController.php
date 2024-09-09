<?php

namespace App\Http\Controllers;
use App\Helpers\QRImageWithLogo;
use App\Models\Customer;
use App\Models\Invoices;
use Carbon\Carbon;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use PDF;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoices $invoices)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoices $invoices)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoices $invoices)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoices $invoices)
    {
        //
    }


    public function generateInvoicePdf($invoiceId,$invoicetype)
    {
        // Fetch the invoice details
        $todayDate = Carbon::now()->format('jS F Y');
        $invoice = DB::table('invoices')
            ->where('invoice_id', $invoiceId)->first();

        $customer = Customer::findOrFail($invoice->customer_id);

        $serviceDetails = DB::table('invoice_service_items')
            ->where('invoice_id', $invoice->invoice_id)
            ->get();
        $discountDetails = DB::table('invoice_discount_items')
            ->where('invoice_id', $invoice->invoice_id)
            ->get();

       //dd($invoice,$customer,$serviceDetails, $discountDetails);

        $qrCodeOptions = new QROptions;
        $qrCodeOptions->addLogoSpace    = true;
        $qrCodeOptions->logoSpaceWidth  = 9;
        $qrCodeOptions->logoSpaceHeight = 9;
        $qrCodeOptions->logoSpaceStartX = 10;
        $qrCodeOptions->logoSpaceStartY = 10;

        $qrCode = $this->generateQRCodeImage(route('invoice.pay', $invoice->invoice_id));

        // Load the view and pass the data
        $pdf = PDF::loadView('invoice.invoiceTemplate', [
            'invoice' => $invoice,
            'customer' => $customer,
            'serviceDetails' => $serviceDetails,
            'discountDetails' => $discountDetails,
            'todayDate' => $todayDate,
            'qrCode' => $qrCode
        ]);


        // Handle based on the invoice type
        if ($invoicetype == 1) {
            // Production invoice: Save the PDF to storage and send an email
            $fileName = 'invoices/' . $invoice->invoice_id . '.pdf';
            Storage::put($fileName, $pdf->output());

            // Call the sendInvoiceEmail function to send the invoice email
            $this->sendInvoiceEmail($invoiceId);
            return response()->json(['message' => 'Invoice generated and email sent.']);

        } elseif ($invoicetype == 4) {
            // Just download the PDF file
            return $pdf->download('invoice_' . $invoice->invoice_id . '.pdf');
        } else {
            // Handle other types or default behavior if needed
            return response()->json(['message' => 'Invalid invoice type.'], 400);
        }

    }



    public function sendInvoiceEmail($invoiceId)
    {
        // Get the file path
        $filePath = 'invoices/'.$invoiceId . '.pdf';
        //$fullPath = Storage::path($filePath);

        // Fetch the customer details
        $invoice = DB::table('invoices')->where('invoice_id', $invoiceId)->first();
        $customer = Customer::findOrFail($invoice->customer_id);
       // dd($invoice,$customer,$fullPath,$filePath);
        // Send the email with the PDF attachment
        Mail::to($customer->email)->send(new InvoiceMail($customer, $filePath,'newInvoice'));

        return 'Invoice email sent successfully!';
    }


    public function resendInvoiceEmail($invoiceId)
    {
        // Get the file path
        $filePath = 'invoices/'.$invoiceId . '.pdf';
        //$fullPath = Storage::path($filePath);

        // Fetch the customer details
        $invoice = DB::table('invoices')->where('invoice_id', $invoiceId)->first();
        $customer = Customer::findOrFail($invoice->customer_id);
        // dd($invoice,$customer,$fullPath,$filePath);
        // Send the email with the PDF attachment
        Mail::to($customer->email)->send(new InvoiceMail($customer, $filePath,'resendInvoice'));
        return 'Invoice email sent successfully!';
    }

    public function sendReminder()
    {

        $invoices = DB::table('invoices')
            ->where('payment_due_date', '<', Carbon::now()->subDays(7))
            ->get();

        foreach ($invoices as $invoice) {
        // Get the file path
        $filePath = 'invoices/'.$invoice->invoice_Id . '.pdf';
        // Fetch the customer details
        $customer = Customer::findOrFail($invoice->customer_id)->first();
        // dd($invoice,$customer,$fullPath,$filePath);
        // Send the email with the PDF attachment
        Mail::to($customer->email)->send(new InvoiceMail($customer, $filePath,'sendReminder'));
        return 'Invoice email sent successfully!';

        }
    }


    public function payInvoice($invoiceId)
    {
        $invoice = DB::table('invoices')->where('invoice_id', $invoiceId)->first();
        $invoice = Invoices::findOrFail($invoice->id);
        //dd($invoice);
        if ($invoice->type == 1){

            $invoice->isPaid = 1;
            $invoice->save();
            return '<h1>Invoice '. $invoiceId .' has been paid!</h1>';
        }
        else {
            return '<h1>Invoice '. $invoiceId .' is a proforma, only production invoices can be paid.</h1>';
        }

    }



    private function generateQRCodeImage($link) {
        $options = new QROptions;

        $options->version             = 5;
        $options->outputBase64        = false;
        $options->scale               = 6;
        $options->imageTransparent    = false;
        $options->drawCircularModules = true;
        $options->circleRadius        = 0.45;
        $options->keepAsSquare        = [
            QRMatrix::M_FINDER,
            QRMatrix::M_FINDER_DOT,
        ];
        // ecc level H is required for logo space
        $options->eccLevel            = EccLevel::H;
        $options->addLogoSpace        = true;
        $options->logoSpaceWidth      = 13;
        $options->logoSpaceHeight     = 13;


        $qrcode = new QRCode($options);
        $qrcode->addByteSegment($link);

        $qrOutputInterface = new QRImageWithLogo($options, $qrcode->getQRMatrix());

        $out = $qrOutputInterface->dump(null, public_path('images/test3.png'));


        return 'data:image/png;base64,' . base64_encode($out);
    }
}
