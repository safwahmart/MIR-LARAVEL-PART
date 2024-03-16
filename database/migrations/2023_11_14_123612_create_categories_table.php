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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->nullable();
            $table->integer('product_type_id')->constrained('product_types');
            $table->string('name');
            $table->string('name_bn');
            $table->string('slug');
            $table->string('slug_bn');
            $table->string('title')->nullable();
            $table->string('title_bn')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_desc')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('alt_text_bn')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('serial_number')->nullable();
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
        Schema::dropIfExists('categories');
    }
};
