<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Schema::create('cart_details', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('cart_id');
        //     $table->char('product_id', 36);
        //     $table->integer('quantity');
        //     $table->timestamps();

        //     // $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
        //     // $table->foreign('product_id')->references('_id')->on('products')->onDelete('cascade');
        // });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_details');
    }
};
