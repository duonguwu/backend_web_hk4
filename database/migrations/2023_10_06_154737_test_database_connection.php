<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class TestDatabaseConnection extends Migration
{
    public function up()
    {
        // Kiểm tra kết nối và thử tạo một bản ghi
        Schema::create('test_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        // Xoá bảng kiểm tra nếu đã tạo
        Schema::dropIfExists('test_users');
    }
}

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         //
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         //
//     }
// };
