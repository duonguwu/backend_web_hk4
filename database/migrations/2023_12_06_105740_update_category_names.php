<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateCategoryNames extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->char('_id', 36)->primary();
            $table->string('categoryName');
            $table->text('description')->nullable();
            $table->string('categoryImg')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
