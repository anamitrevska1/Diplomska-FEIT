<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('invoice_id')->unique();
            $table->integer('type');
            $table->bigInteger('user_id');
            $table->bigInteger('customer_id');
            $table->date('from_date');
            $table->date('to_date');
            $table->date('payment_due_date');
            $table->bigInteger('total_amount');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
