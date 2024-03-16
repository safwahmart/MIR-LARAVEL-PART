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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('invoice_no');
            $table->integer('customer_id');
            $table->integer('category_id')->nullable();
            $table->integer('warehouse_id');
            $table->integer('sale_by')->nullable();
            $table->date('sale_date');
            $table->double('sub_total')->nullable();
            $table->double('discount_amount')->nullable();
            $table->double('vat')->nullable();
            $table->double('payable')->nullable();
            $table->double('rounding')->nullable();
            $table->double('change')->nullable();
            $table->double('paid_amount')->default(0);
            $table->integer('serial');
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
        Schema::dropIfExists('sales');
    }
};
