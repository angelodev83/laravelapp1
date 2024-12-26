<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SupplyItem;
use App\Models\SupplyOrderItem;
use App\Models\SupplyOrder;

class SupplyItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SupplyOrder::truncate();
        SupplyOrderItem::truncate();
        SupplyItem::truncate();
        SupplyItem::insert([
            ['wholesaler_name'=>'Staples','item_number'=> 2609730, 'model_number' => 'Z6640EX', 'description' => 'Blue 30-33 Gallon Trash Bag (250ct)'],
            ['wholesaler_name'=>'Staples','item_number'=> 818869, 'model_number' => 'STOG3340E11', 'description' => 'Green 30-33 Gallon Trash Bag (40ct)'],
            ['wholesaler_name'=>'Staples','item_number'=> 404369, 'model_number' => 'CW57401', 'description' => 'Clear Trash Bags (100ct)'],
            ['wholesaler_name'=>'Staples','item_number'=> 1149611, 'model_number' => '26860-CC', 'description' => '8 Reams of Paper'],
            ['wholesaler_name'=>'Staples','item_number'=> 72216, 'model_number' => 'CW24776', 'description' => 'Toilet Seat Covers'],
            ['wholesaler_name'=>'Staples','item_number'=> 24443474, 'model_number' => 'SA050-CCC', 'description' => 'Alcohol Wipes'],
            ['wholesaler_name'=>'Staples','item_number'=> 848352, 'model_number' => '91554', 'description' => 'Scotts Foaming Hand Soap'],
            ['wholesaler_name'=>'Staples','item_number'=> 538066, 'model_number' => '13954', 'description' => 'Avery Laser Color Coded Labels (1008ct)'],

            ['wholesaler_name'=>'McKesson','item_number'=> 1258250, 'model_number' => null, 'description' => 'Center Amber Vials 13 Drum'],
            ['wholesaler_name'=>'McKesson','item_number'=> 1260348, 'model_number' => null, 'description' => 'Center Amber Vials 16 Drum'],
            ['wholesaler_name'=>'McKesson','item_number'=> 1262468, 'model_number' => null, 'description' => 'Center Amber Vials 30 Drum'],
            ['wholesaler_name'=>'McKesson','item_number'=> 1265149, 'model_number' => null, 'description' => 'Center Amber Vials 60 Drum'],
            ['wholesaler_name'=>'McKesson','item_number'=> 2513091, 'model_number' => null, 'description' => 'Center Amber Vials 13 Drum Lids'],
            ['wholesaler_name'=>'McKesson','item_number'=> 2513216, 'model_number' => null, 'description' => 'Center Amber Vials 16 Drum Lids'],
            ['wholesaler_name'=>'McKesson','item_number'=> 2513257, 'model_number' => null, 'description' => 'Center Amber Vials 30/60 Drum Lids'],
            ['wholesaler_name'=>'McKesson','item_number'=> 1502574, 'model_number' => null, 'description' => 'Center Amber Vials 2 oz Liquid Bottle'],
            ['wholesaler_name'=>'McKesson','item_number'=> 160243, 'model_number' => null, 'description' => 'Center Amber Vials 4 oz Liquid Bottle'],
            ['wholesaler_name'=>'McKesson','item_number'=> 1602416, 'model_number' => null, 'description' => 'Center Amber Vials 6 oz Liquid Bottle'],
            ['wholesaler_name'=>'McKesson','item_number'=> 1602317, 'model_number' => null, 'description' => 'Center Amber Vials 12 oz Liquid Bottle'],
            ['wholesaler_name'=>'McKesson','item_number'=> 1602267, 'model_number' => null, 'description' => 'Center Amber Vials 16 oz Liquid Bottle'],
            ['wholesaler_name'=>'McKesson','item_number'=> 1366723, 'model_number' => null, 'description' => 'Center Amber Vials Zip Tie (use to seal McKesson totes when Returning Products)'],
            ['wholesaler_name'=>'McKesson','item_number'=> 1285766, 'model_number' => null, 'description' => 'Center Amber Vials 1 ml Oral Syringes'],
            ['wholesaler_name'=>'McKesson','item_number'=> 1285873, 'model_number' => null, 'description' => 'Center Amber Vials 5 ml Oral Syringes'],
            ['wholesaler_name'=>'McKesson','item_number'=> 1824606, 'model_number' => null, 'description' => 'Center Amber Vials 10 ml Oral Syringes'],

            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'H-5148BLU', 'description' => 'Blue Uline Thin Trash Can (23 Gal)'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'H-5148G', 'description' => 'Green Uline Thin Trash Can (23 Gal)'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'H-1859BLU', 'description' => 'Blue Trash Bin (10 Gal)'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-13527BL', 'description' => 'Black Trash Bin (10 Gal)'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-17150', 'description' => 'MAPA Trilites Chemical Resistant Gloves (Chemo Gloves)'],

            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-7887', 'description' => 'Insulated Shipping Kits 8 x 6 1/2 x 5"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-19762', 'description' => 'Insulated Shipping Kits 8 1/2 x 7 1/2 x 7"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-22602', 'description' => 'Insulated Shipping Kits 8 1/2 x 7 1/2 x 9"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-9903', 'description' => 'Insulated Shipping Kits 11 x 9 x 7 1/4"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-13391', 'description' => 'Insulated Shipping Kits 11 x 9 x 10"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-7359', 'description' => 'Insulated Shipping Kits 11 x 9 x 12"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-11355', 'description' => 'Insulated Shipping Kits 11 x 9 x 15"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-12682', 'description' => 'Insulated Shipping Kits 12 x 12 x 9 1/2 x 1/2"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-21090', 'description' => 'Insulated Shipping Kits 12 3/4 x 12 3/4 x 6 5/8"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-16478', 'description' => 'Insulated Shipping Kits 13 1/1 x 11 1/4 x 12 1/2"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-11356', 'description' => 'Insulated Shipping Kits 15 x 13 x 8"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-15181', 'description' => 'Insulated Shipping Kits 15 x 13 x 10"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-7360', 'description' => 'Insulated Shipping Kits 15 x 13 x 12"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-21091', 'description' => 'Insulated Shipping Kits 15 x 13 x 18"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-13392', 'description' => 'Insulated Shipping Kits 16 x 16 x 15 1/2"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-11357', 'description' => 'Insulated Shipping Kits 16 x 16 x 15 1/2"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-11357', 'description' => 'Insulated Shipping Kits 16 x 16 x 15 1/2"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-16479', 'description' => 'Insulated Shipping Kits 16 1/4 x 14 1/4 x 14 3/8"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-21528', 'description' => 'Insulated Shipping Kits 17 1/2 x 14 5/8 x 14"'],

            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-19785', 'description' => 'Single Use Cold Packs 3 oz'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-18966', 'description' => 'Single Use Cold Packs 6 oz'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-18252', 'description' => 'Single Use Cold Packs 8 oz'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-18967', 'description' => 'Single Use Cold Packs 12 oz'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-18253', 'description' => 'Single Use Cold Packs 16 oz'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-18254', 'description' => 'Single Use Cold Packs 24 oz'],

            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-19285', 'description' => 'Time & Temperature Sensitive Labels 4 1/4 x 4 1/4"'],
            ['wholesaler_name'=>'Uline','item_number'=> null, 'model_number' => 'S-3851', 'description' => 'Freeze Indicator'],
        ]);
    }
}
