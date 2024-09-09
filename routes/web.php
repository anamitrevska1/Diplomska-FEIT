<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DiscountController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

    Route::resource('customer', CustomerController::class);
    Route::put('/customer/suppress/{id}', 'CustomerController@suppress');
    Route::put('/customer/suppress/{id}', [CustomerController::class, 'suppress'])->name('customer.suppress');
    Route::post('/customer/customerServiceAdd', [CustomerController::class, 'customerServiceAdd'])->name('customer.customerServiceAdd');
    Route::put('/customer/deactivateService/{id}', [CustomerController::class, 'deactivateService'])->name('customer.deactivateService');
    Route::post('/customer/customerDiscountAdd', [CustomerController::class, 'customerDiscountAdd'])->name('customer.customerDiscountAdd');
    Route::put('/customer/deactivateDiscount/{id}', [CustomerController::class, 'deactivateDiscount'])->name('customer.deactivateDiscount');
    Route::get('/customer/proforma/{id}', [CustomerController::class, 'calculateProformaBill'])->name('customer.proformaInvoice');
    Route::get('/customer/mainBill/{id}', [CustomerController::class, 'calculateMonthlyBill'])->name('customer.mainInvoice');


//Service routes

    Route::get('/service', [ServiceController::class, 'index'])->name('service.index');
    Route::post('/service', [ServiceController::class, 'store'])->name('service.store');
    Route::get('/service/{id}', [ServiceController::class, 'show'])->name('service.show');
    Route::get('/service/{id}/edit', [ServiceController::class, 'edit'])->name('service.edit');
    Route::put('/service/{id}', [ServiceController::class, 'update'])->name('service.update');
    Route::delete('/service/{id}', [ServiceController::class, 'destroy'])->name('service.destroy');
    Route::get('/service/create', [ServiceController::class, 'create'])->name('service.create');

//Discount routes

    Route::get('/discount', [DiscountController::class, 'index'])->name('discount.index');
    Route::post('/discount', [DiscountController::class, 'store'])->name('discount.store');
    Route::get('/discount/{id}', [DiscountController::class, 'show'])->name('discount.show');
    Route::get('/discount/{id}/edit', [DiscountController::class, 'edit'])->name('discount.edit');
    Route::put('/discount/{id}', [DiscountController::class, 'update'])->name('discount.update');
    Route::delete('/discount/{id}', [DiscountController::class, 'destroy'])->name('discount.destroy');
    Route::get('/discount/create', [DiscountController::class, 'create'])->name('discount.create');

// Invoice routes

    Route::get('/invoice/{invoiceId}/pdf', [InvoiceController::class, 'generateInvoicePdf'])->name('invoice.pdf');
    Route::get('/invoice/{invoiceId}/email', [InvoiceController::class, 'sendInvoiceEmail'])->name('invoice.email');
    Route::get('/invoice/{invoiceId}/email', [InvoiceController::class, 'sendInvoiceEmail'])->name('invoice.email');
    Route::get('/download/{invoiceId}', function ($invoiceId) {
        return Storage::download('invoices/' . $invoiceId . '.pdf');
    });
    Route::get('/invoice/resend/{invoiceId}', [InvoiceController::class, 'resendInvoiceEmail'])->name('ResendInvoice.pdf');

});

require __DIR__.'/auth.php';

Route::get('/invoice/pay/{invoiceId}', [InvoiceController::class, 'payInvoice'])->name('invoice.pay');



