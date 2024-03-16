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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->integer('use_type_id');
            $table->string('name')->unique();
            $table->string('name_bn')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('discount_type_id');
            $table->double('amount');
            $table->double('max_discount_amount')->nullable();
            $table->text('description')->nullable();
            $table->text('description_bn')->nullable();
            $table->boolean('highlight')->default(true);
            $table->boolean('coupon_apply_status')->default(true);
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
        Schema::dropIfExists('coupons');
    }
};
