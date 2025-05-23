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
        Schema::table('assign_p_t_s', function (Blueprint $table) {
            $table->string('payment_type')->nullable()->after('payment_method');
            $table->string('received_amt')->default(0)->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assign_p_t_s', function (Blueprint $table) {
            //
        });
    }
};
