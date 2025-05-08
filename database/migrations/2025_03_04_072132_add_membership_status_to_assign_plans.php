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
        Schema::table('assign_plans', function (Blueprint $table) {
            $table->enum('membership_status', ['active','trial','expired','canceled'])->default('active')->after('utr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assign_plans', function (Blueprint $table) {
            $table->dropColumn('membership_status');
        });
    }
};
