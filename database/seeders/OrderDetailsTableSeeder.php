<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        for($i=1; $i<=10000; $i++){
            $product = Product::inRandomOrder()->first();
            OrderDetails::create([
                'order_id' => Order::inRandomOrder()->first()->id,
                'product_name' => $product->title,
                'category' => $product->category->title,
                'amount' => 3,
                'piece_price' => $product->discount,
                'price' => $product->discount * 3
            ]);
        }
    }
}
