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
        Schema::create('invoice_discount_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('invoice_id');
            $table->bigInteger('user_id');
            $table->bigInteger('customer_id');
            $table->bigInteger('service_id')->nullable();
            $table->string('discount_name');
            $table->date('from_date');
            $table->date('to_date');
            $table->bigInteger('discount_amount')->nullable();
            $table->bigInteger('discount_percentage')->nullable();
            $table->string('discount_on_service_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_discount_items');
    }
};
