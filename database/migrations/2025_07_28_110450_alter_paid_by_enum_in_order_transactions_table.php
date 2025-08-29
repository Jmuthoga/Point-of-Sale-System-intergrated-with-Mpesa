<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_transactions', function (Blueprint $table) {
            $table->enum('paid_by', ['cash', 'mpesa', 'bank'])
                  ->comment('Allowed: cash, mpesa, bank')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_transactions', function (Blueprint $table) {
            $table->string('paid_by')->change();
        });
    }
};