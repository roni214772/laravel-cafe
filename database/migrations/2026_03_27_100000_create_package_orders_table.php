<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Platform bilgisi
            $table->string('platform', 30); // trendyol, yemeksepeti, getir, telefon, diger
            $table->string('platform_order_id', 100)->nullable(); // Platformdaki sipariş no

            // Müşteri bilgisi
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_phone', 30)->nullable();
            $table->string('customer_address', 500)->nullable();
            $table->string('customer_note', 500)->nullable();

            // Durum
            $table->enum('status', ['new','preparing','ready','on_way','delivered','cancelled'])->default('new');

            // Tutarlar
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            // Ödeme
            $table->string('payment_method', 30)->default('platform'); // platform, cash, card
            $table->boolean('is_paid')->default(false);

            // Zaman damgaları
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('package_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 100);
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('total', 10, 2);
            $table->string('note', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_order_items');
        Schema::dropIfExists('package_orders');
    }
};
