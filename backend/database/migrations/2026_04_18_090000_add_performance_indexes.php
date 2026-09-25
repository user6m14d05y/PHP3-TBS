<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm indexes để tăng tốc các query thường dùng.
     * 
     * Trước: MySQL full table scan mỗi lần gọi GET /api/product
     * Sau:   MySQL dùng index → nhanh gấp 10-50x trên bảng lớn
     */
    public function up(): void
    {
        // Index composite cho query: WHERE is_active = 1 ORDER BY created_at DESC
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'created_at'], 'idx_products_active_created');
            $table->index('category_id', 'idx_products_category');
            $table->index('category_item_id', 'idx_products_category_item');
        });

        // Index cho product_images — dùng khi eager load images
        Schema::table('product_images', function (Blueprint $table) {
            $table->index(['product_id', 'sort_order'], 'idx_images_product_sort');
        });

        // Index cho product_variants — dùng khi eager load variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->index(['product_id', 'price'], 'idx_variants_product_price');
        });

        // Index cho contact email lookup
        Schema::table('contact', function (Blueprint $table) {
            $table->unique('email', 'idx_contact_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_active_created');
            $table->dropIndex('idx_products_category');
            $table->dropIndex('idx_products_category_item');
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex('idx_images_product_sort');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('idx_variants_product_price');
        });

        Schema::table('contact', function (Blueprint $table) {
            $table->dropUnique('idx_contact_email');
        });
    }
};
