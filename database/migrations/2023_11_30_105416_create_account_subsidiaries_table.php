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
        Schema::create('account_subsidiaries', function (Blueprint $table) {
            $table->id();
            $table->integer('account_group_id')->constrained('account_groups');
            $table->integer('account_control_id')->constrained('account_controls');
            $table->string('name');
            $table->string('name_bn');
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
        Schema::dropIfExists('account_subsidiaries');
    }
};
