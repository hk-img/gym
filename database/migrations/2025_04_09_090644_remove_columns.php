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
        if (Schema::hasColumn('calories', 'protein', 'carbs' , 'fats')) {

            Schema::table('meals', function (Blueprint $table) {
                $table->dropColumn('calories');
                $table->dropColumn('protein');
                $table->dropColumn('carbs');
                $table->dropColumn('fats');
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meals', function($table) {
            $table->integer('calories');
            $table->integer('protein');
            $table->integer('carbs');
            $table->integer('fats');
        });
    }
};
