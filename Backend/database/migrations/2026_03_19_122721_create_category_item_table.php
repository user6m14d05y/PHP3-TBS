<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_item', function (Blueprint $table) {
            $table->integer('id', true); // auto increment
            $table->integer('category_id'); // phải cùng kiểu INT
            $table->string('name', 50)->nullable();
            $table->timestamps();

            // tạo khóa ngoại
            $table->foreign('category_id')
                ->references('id')
                ->on('category')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_item');
    }
};
