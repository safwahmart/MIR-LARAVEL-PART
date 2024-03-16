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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_type_id')->constrained('customer_types');
            $table->string('name');
            $table->string('name_bn');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->boolean('gender')->nullable();
            $table->text('address')->nullable();
            $table->string('country')->nullable();
            $table->integer('district')->nullable();
            $table->integer('area')->nullable();
            $table->double('zip_code')->nullable();
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
        Schema::dropIfExists('customers');
    }
};
