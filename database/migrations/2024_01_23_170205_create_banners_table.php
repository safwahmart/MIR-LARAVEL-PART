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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('banner_name')->nullable();
            $table->string('banner_name_bn')->nullable();
            $table->string('banner_url')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_title_bn')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_description_bn')->nullable();
            $table->text('alt_text')->nullable();
            $table->text('alt_text_bn')->nullable();
            $table->string('image')->nullable();
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
        Schema::dropIfExists('banners');
    }
};
