<?php

use App\Models\Business;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::create([
            'name' => 'টন',
            'code' => '0001',
            'labels' => 'টন/বস্তা/কেজি',
            'relation' => '1/20/50',
            'description' => '১ টন = ২০ বস্তা, ১ বস্তা = ৫০ কেজি',
            'business_id' => Business::first()->id,
        ]);
    }
}
