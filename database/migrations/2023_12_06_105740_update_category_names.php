<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateCategoryNames extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('categoryName', 255)->change();
        });

        DB::table('categories')->update([
            'categoryName' => DB::raw("CONCAT(UCASE(LEFT(categoryName, 1)), LCASE(SUBSTRING(categoryName FROM 2)))"),
        ]);
    }

    public function down()
    {
        // Ngược lại thay đổi trong trường hợp rollback
    }
}
