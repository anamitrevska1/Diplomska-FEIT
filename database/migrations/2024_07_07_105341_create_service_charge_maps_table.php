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
        Schema::create('service_charge_maps', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('customer_id');
            $table->bigInteger('service_id');
            $table->date('active_Date');
            $table->date('billed_through_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('service_type');
            $table->bigInteger('invoice_ref_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_charge_maps');
    }
};
