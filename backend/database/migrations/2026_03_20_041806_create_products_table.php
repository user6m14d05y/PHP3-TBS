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
            $table->integer('id', true); // auto increment, primary key

            // Danh mục cha và con
            $table->integer('category_id')->nullable();       // Danh mục cha (category)
            $table->integer('category_item_id')->nullable();  // Danh mục con (category_item)

            $table->string('name', 100);
            $table->string('slug', 120)->unique();            // URL thân thiện, ví dụ: hoa-hong-do
            $table->text('description')->nullable();          // Mô tả chi tiết sản phẩm
            $table->string('thumbnail')->nullable();          // Ảnh đại diện chính
            $table->boolean('is_active')->default(true);      // Hiển thị / Ẩn sản phẩm

            $table->timestamps();

            // Khóa ngoại
            $table->foreign('category_id')
                ->references('id')
                ->on('category')
                ->onDelete('set null');

            $table->foreign('category_item_id')
                ->references('id')
                ->on('category_item')
                ->onDelete('set null');
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

