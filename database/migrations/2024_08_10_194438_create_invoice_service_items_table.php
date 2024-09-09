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
        Schema::create('invoice_service_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('invoice_id');
            $table->bigInteger('user_id');
            $table->bigInteger('customer_id');
            $table->bigInteger('service_id');
            $table->string('service_name');
            $table->date('from_date');
            $table->date('to_date');
            $table->bigInteger('active_days_in_month');
            $table->bigInteger('price_per_month');
            $table->bigInteger('prorated_price');
            $table->bigInteger('service_discount');
            $table->bigInteger('total_service_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_service_items');
    }
};
