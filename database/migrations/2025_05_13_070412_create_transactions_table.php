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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('gym_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('table_id')->nullable();
            $table->string('type')->nullable();
            $table->string('received_amt')->nullable();
            $table->string('balance_amt')->nullable();
            $table->string('total_amt')->nullable();
            $table->string('payment_type')->nullable();
            $table->enum('status',['pending','cleared'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
