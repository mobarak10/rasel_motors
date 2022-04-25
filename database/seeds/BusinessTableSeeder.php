<?php

use App\Models\Business;
use Illuminate\Database\Seeder;

class BusinessTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Business::create([
            'name' => 'মেসার্স ভাই ভাই ট্রেডার্স',
            'thumbnail' => '6798326',
            'address' => 'মেইন রোড, ত্রিশাল বাজার ময়মনসিংহ।',
            'phone' => '01749-941011'
        ]);
    }
}
