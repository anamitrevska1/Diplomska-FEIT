<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Invoices extends Model
{
    use HasFactory;


    protected $fillable = [
        'type', 'user_id', 'customer_id','from_date','to_date','payment_due_date','total_amount','isPaid'
    ];
    public static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            DB::transaction(function () use ($invoice) {
                $latestInvoice = self::lockForUpdate()->latest()->first();

                if (!$latestInvoice) {
                    $invoice->invoice_id = 'INV0001';
                } else {
                    $number = intval(substr($latestInvoice->invoice_id, 3)) + 1;
                    $invoice->invoice_id = 'INV' . str_pad($number, 4, '0', STR_PAD_LEFT);
                }
            });
        });
    }
}
