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
        Schema::create('products', function (Blueprint $table) {
            $table->char('_id', 36)->primary();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('category')->nullable();
            $table->string('gender')->nullable();
            $table->string('weight')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('image')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->integer('price')->nullable();
            $table->integer('newPrice')->nullable();
            $table->tinyInteger('trending')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
