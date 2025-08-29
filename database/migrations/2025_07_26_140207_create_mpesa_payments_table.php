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
        Schema::create('mpesa_payments', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_type')->nullable();
            $table->string('trans_id')->unique(); // MPESA CODE
            $table->string('trans_time');
            $table->string('business_short_code');
            $table->string('bill_ref_number')->nullable(); // Account Number - we'll treat this as order_id
            $table->string('invoice_number')->nullable();

            $table->decimal('trans_amount', 10, 2);
            $table->string('msisdn'); // Customer Phone
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();

            // Our POS Order ID (mapped from account number)
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_payments');
    }
};
