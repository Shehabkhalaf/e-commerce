<?php

namespace Database\Seeders;

use App\Models\Order;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $status = ['pending','completed','refunded'];
        $faker = Factory::create();
        for ($i = 1; $i<=1000; $i++){
            Order::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'address' => $faker->address,
                'governorate' => $faker->state,
                'city' => $faker->city,
                'postal' => $faker->postcode,
                'phone' => $faker->e164PhoneNumber,
                'status' => $status[rand(0,2)],
                'promocode' => 'nothing',
                'total_price' => $faker->randomFloat(2, 50, 200),
                'created_at' => $faker->dateTimeThisYear->getTimestamp(),
            ]);
        }
    }
}
