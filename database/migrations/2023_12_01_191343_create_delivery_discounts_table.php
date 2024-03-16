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
        Schema::create('delivery_discounts', function (Blueprint $table) {
            $table->id();
            $table->boolean('cod_inside_dhaka')->default(false);
            $table->double('inside_cod_dhaka')->nullable();
            $table->boolean('cod_outside_dhaka')->default(false);
            $table->double('outside_cod_dhaka')->nullable();
            $table->boolean('free_delivery')->default(true);
            $table->double('min_purchase_amount')->nullable();
            $table->double('free_delivery_amount')->nullable();
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
        Schema::dropIfExists('delivery_discounts');
    }
};
