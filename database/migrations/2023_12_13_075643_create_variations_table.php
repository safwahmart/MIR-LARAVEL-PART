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
        Schema::create('variations', function (Blueprint $table) {
            $table->id();
            $table->integer('product_upload_id')->nullable();
            $table->string('name');
            $table->string('name_bn');
            $table->string('sku')->nullable();
            $table->string('code')->nullable();
            $table->double('product_price')->nullable();
            $table->double('sale_price')->nullable();
            $table->double('opening_quantity')->nullable();
            $table->integer('warehouse_id')->nullable();
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
        Schema::dropIfExists('variations');
    }
};
