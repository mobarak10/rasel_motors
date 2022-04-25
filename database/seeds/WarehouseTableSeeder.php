<?php

use App\Models\Business;
use App\Models\User\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Warehouse::create([
            'code' => 'WAH0001',
            'title' => 'Warehouse 1',
            'address' => 'ত্রিশাল বাজার',
            'user_id' => User::first()->id,
            'status' => 1,
            'business_id' => Business::first()->id,
        ]);
    }
}
