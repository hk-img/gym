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
        Schema::create('gym_working_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_id'); // Gym ID
            $table->string('day'); // Monday, Tuesday, etc.
            $table->time('open_time');
            $table->time('close_time');
            $table->boolean('is_closed')->default(false); // If gym is closed on that day
            $table->timestamps();

            // Foreign Key
            $table->foreign('gym_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_working_hours');
    }
};
