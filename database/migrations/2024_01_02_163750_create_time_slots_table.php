<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('time_slot_name')->unique();
            $table->string('time_slot_name_bn')->unique();
            $table->string('starting_time')->nullable();
            $table->string('ending_time')->nullable();
            $table->string('disable_at')->nullable();
            $table->boolean('status')->default(true);            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_slots');
    }
};
