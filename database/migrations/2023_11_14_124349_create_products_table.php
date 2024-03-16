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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('assign_user_id')->nullable();
            $table->integer('hightlight_type_id')->nullable();
            $table->integer('warehouse_id')->nullable();
            $table->integer('category_id');
            $table->integer('brand_id')->nullable();
            $table->integer('unit_id');
            $table->integer('country_id')->nullable();
            $table->string('product_tag')->nullable();
            $table->string('unit');
            $table->string('product_name');
            $table->string('product_name_bn');
            $table->string('product_slug')->nullable();
            $table->string('product_code')->nullable();
            $table->string('product_sku')->nullable();
            $table->string('mfg_model_no')->nullable();
            $table->string('barcode')->nullable();
            $table->string('weight')->nullable();
            $table->string('alert_quantity')->nullable();
            $table->string('max_order_quantity')->nullable();
            $table->double('purchase_price')->nullable();
            $table->double('wholesale_price')->nullable();
            $table->double('sale_price')->nullable();
            $table->double('app_price')->nullable();
            $table->double('discount')->nullable();
            $table->double('vat')->nullable();
            $table->integer('vat_type')->nullable();
            $table->double('discount_flat')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('video_link')->nullable();
            $table->string('expire_date')->nullable();
            $table->string('expire_note')->nullable();
            $table->string('lot')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->string('video_thumbnail')->nullable();
            $table->double('opening_qty')->nullable();
            $table->text('short_desc')->nullable();
            $table->text('meta_desc')->nullable();
            $table->text('alt_text')->nullable();
            $table->text('desc')->nullable();
            $table->boolean('is_variation')->default(0);
            $table->boolean('is_refundable')->default(0);
            $table->boolean('is_unit_visible')->default(0);
            $table->boolean('is_stock_visible')->default(0);
            $table->boolean('is_upload')->default(0);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('products');
    }
};
