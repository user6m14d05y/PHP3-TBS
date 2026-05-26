<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 40)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained('shops')->nullOnDelete();
            $table->foreignId('user_address_id')->nullable()->constrained('user_addresses')->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->string('recipient_name');
            $table->string('recipient_phone', 20);
            $table->string('recipient_email')->nullable();
            $table->string('shipping_address');
            $table->decimal('shipping_latitude', 10, 7)->nullable();
            $table->decimal('shipping_longitude', 10, 7)->nullable();
            $table->decimal('delivery_distance_km', 6, 2)->nullable();
            $table->decimal('subtotal_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'awaiting_payment', 'paid', 'processing', 'shipping', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'failed', 'refunded'])->default('unpaid');
            $table->string('payment_method', 50)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['shop_id', 'status']);
            $table->index('payment_status');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->integer('product_variant_id');
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->string('sku', 100)->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 12, 2);
            $table->timestamps();

            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->restrictOnDelete();

            $table->index('product_variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
