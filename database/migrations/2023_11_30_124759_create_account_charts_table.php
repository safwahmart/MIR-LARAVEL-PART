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
        Schema::create('account_charts', function (Blueprint $table) {
            $table->id();
            $table->integer('account_group_id')->constrained('account_groups');
            $table->integer('account_control_id')->constrained('account_controls');
            $table->integer('account_subsidiary_id')->constrained('account_subsidiaries');
            $table->string('account_name');
            $table->string('account_name_bn');
            $table->string('remark');
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
        Schema::dropIfExists('account_charts');
    }
};
