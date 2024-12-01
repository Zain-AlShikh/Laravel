<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المنتج
            $table->text('description'); // وصف المنتج
            $table->decimal('price', 8, 2); // سعر المنتج
            $table->integer('quantity'); // كمية المنتج
            $table->foreignId('store_id')->constrained('stores'); // ارتباط المنتج بالمتجر
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}
