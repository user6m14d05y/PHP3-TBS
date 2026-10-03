<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_id');              // Thuộc sản phẩm nào
            $table->integer('size_id')->nullable();      // Kích thước (S, M, L, XL...)

            $table->decimal('price', 10, 2);             // Giá của biến thể này
            $table->decimal('sale_price', 10, 2)->nullable(); // Giá khuyến mãi (nếu có)
            $table->integer('stock')->default(0);        // Tồn kho biến thể này
            $table->string('sku', 100)->unique()->nullable(); // Mã SKU riêng (nếu cần)
            $table->boolean('is_active')->default(true); // Bật/tắt biến thể này

            $table->timestamps();

            // Khóa ngoại
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->foreign('size_id')
                ->references('id')
                ->on('size')
                ->onDelete('set null');

            // Mỗi sản phẩm không được có 2 biến thể trùng size
            $table->unique(['product_id', 'size_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
