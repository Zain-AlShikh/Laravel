<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // استدعاء Seeder للمنتجات
        $this->call(ProductSeeder::class);
    }
}
