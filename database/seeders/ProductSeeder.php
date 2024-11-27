<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // إضافة منتجات للمتاجر
        Product::create([
            'name' => 'Laptop',
            'description' => 'High performance laptop.',
            'price' => 1500,
            'quantity' => 10,
            'store_id' => 1, // متجر الإلكترونيات
        ]);

        Product::create([
            'name' => 'T-Shirt',
            'description' => 'Comfortable cotton T-shirt.',
            'price' => 30,
            'quantity' => 50,
            'store_id' => 2, // متجر الألبسة
        ]);

        Product::create([
            'name' => 'Rice',
            'description' => 'Premium quality rice.',
            'price' => 25,
            'quantity' => 100,
            'store_id' => 3, // متجر الغذائيات
        ]);

        Product::create([
            'name' => 'Washing Machine',
            'description' => 'Durable washing machine.',
            'price' => 500,
            'quantity' => 5,
            'store_id' => 4, // متجر الأدوات المنزلية
        ]);
    }
}
