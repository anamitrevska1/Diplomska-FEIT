<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;


    protected $customer;
    protected $filePath;

    /**
     * Create a new message instance.
     */
    public function __construct($customer, $filePath, $type)
    {
        $this->customer = $customer;
        $this->filePath = $filePath;
        $this->type = $type;
    }

    public function build()
    {

        if($this->type == "newInvoice"){
        return $this->view('invoice.emailTemplate')
            ->subject('Your Invoice')
            ->attachFromStorage($this->filePath, 'invoice.pdf', [
                'mime' => 'application/pdf',
            ])
            ->with(['customer' => $this->customer]);

        }
        else if($this->type == "resendInvoice"){
            return $this->view('invoice.ResendInvoiceTemplate')
                ->subject('Your Invoice')
                ->attachFromStorage($this->filePath, 'invoice.pdf', [
                    'mime' => 'application/pdf',
                ])
                ->with(['customer' => $this->customer]);
        }
        else if($this->type == "sendReminder"){
            return $this->view('invoice.ReminderEmail')
                ->subject('Your Invoice')
                ->attachFromStorage($this->filePath, 'invoice.pdf', [
                    'mime' => 'application/pdf',
                ])
                ->with(['customer' => $this->customer]);
        }
    }


}
