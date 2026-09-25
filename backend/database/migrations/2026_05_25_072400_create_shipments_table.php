<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('provider', 50)->nullable();
            $table->string('tracking_code')->nullable();
            $table->decimal('quoted_fee', 12, 2)->default(0);
            $table->decimal('distance_km', 6, 2)->nullable();
            $table->enum('status', ['quoted', 'created', 'picking', 'shipping', 'delivered', 'cancelled', 'failed'])->default('quoted');
            $table->json('quote_payload')->nullable();
            $table->json('provider_payload')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['provider', 'tracking_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
