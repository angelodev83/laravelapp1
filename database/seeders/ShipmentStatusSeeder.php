<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShipmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *  
     */
    public function run(): void
    {

        DB::table('shipment_statuses')->insert([
                
             //Label to be created,Label Created, Label printed, Picked up, In Transit, Delivered, On Hold
            ['name' => 'Label to be created', 'description' => 'Label to be created', 'color' => '#000000'],
            ['name' => 'Label Created', 'description' => 'Label Created', 'color' => '#000000'],
            ['name' => 'Label printed', 'description' => 'Label printed', 'color' => '#000000'],
            ['name' => 'Picked up', 'description' => 'Picked up', 'color' => '#000000'],
            ['name' => 'In Transit', 'description' => 'In Transit', 'color' => '#000000'],
            ['name' => 'Delivered', 'description' => 'Delivered', 'color' => '#000000'],
            ['name' => 'On Hold', 'description' => 'On Hold', 'color' => '#000000'],
        
        ]);
    }
}
