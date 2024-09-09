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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('created_by');
            $table ->string('customer_type');
            $table ->string('first_name');
            $table ->string('last_name');
            $table ->string('company_name');
            $table ->string('email');
            $table ->string('phone');
            $table ->string('address');
            $table ->string('city');
            $table ->string('zip');
            $table ->date('prev_cutoff_date');
            $table ->date('prev_bill_date')->nullable();
            $table ->date('next_bill_date')->nullable();
            $table ->integer('bill_period')->nullable();
            $table -> boolean('no_bill')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
