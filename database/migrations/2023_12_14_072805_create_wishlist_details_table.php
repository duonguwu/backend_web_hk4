<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wishlist_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('wishlist_id')->unsigned();
            $table->char('product_id', 36)->collation('utf8mb4_general_ci');
            $table->timestamps();

            $table->foreign('wishlist_id')->references('id')->on('wishlist')->onDelete('cascade');
            $table->foreign('product_id')->references('_id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_details');
    }
};
