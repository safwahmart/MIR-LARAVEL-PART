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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('invoice_no');
            $table->integer('customer_id');
            $table->integer('warehouse_id');
            $table->date('date');
            $table->integer('sale_by');
            $table->date('order_date');
            $table->date('delivery_date')->nullable();
            $table->integer('time_slot_id')->nullable();
            $table->double('discount_amount')->nullable();
            $table->double('special_discount_amount')->nullable();
            $table->double('sub_total')->nullable();
            $table->double('vat')->nullable();
            $table->double('shipping_cost')->nullable();
            $table->double('cod_charge')->nullable();
            $table->double('rounding')->nullable();
            $table->double('payable')->nullable();
            $table->double('paid_amount')->default(0);
            $table->text('order_note')->nullable();
            $table->integer('serial');
            $table->boolean('payment_type');
            $table->boolean('source');
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('orders');
    }
};
