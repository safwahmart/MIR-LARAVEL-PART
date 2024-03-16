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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_bn');
            $table->string('slug');
            $table->string('slug_bn');
            $table->string('title')->nullable();
            $table->string('title_bn')->nullable();
            $table->string('position')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('alt_text_bn')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('show_menu')->default(true);
            $table->boolean('highlight')->default(true);
            $table->boolean('status')->default(true);
            $table->text('meta_desc')->nullable();
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
        Schema::dropIfExists('brands');
    }
};
