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
        Schema::create('popup_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('title_bn')->unique();
            $table->text('description')->nullable();
            $table->text('description_bn')->nullable();
            $table->string('image');
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
        Schema::dropIfExists('popup_notifications');
    }
};
