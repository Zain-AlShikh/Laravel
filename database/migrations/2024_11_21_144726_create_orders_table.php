<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ارتباط الطلب بالمستخدم
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // ارتباط الطلب بالمنتج
            $table->integer('quantity'); // كمية المنتج في الطلب
            $table->decimal('total_price', 8, 2); // إجمالي السعر
            $table->enum('status', ['pending', 'delivered', 'cancelled']); // حالة الطلب
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}
