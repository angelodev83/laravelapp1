<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransferTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        DB::statement('TRUNCATE table transfer_tasks');
        DB::table('transfer_tasks')->insert([
            [
                'name'          => 'COMPLETE', 
                'description'   => 'complete',
                'color'         => '#008844',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle-check',
                'sort'          => 15,
            ],
            [
                'name'          => 'TO DO', 
                'description'   => 'to do',
                'color'         => '#e8eaed',
                'text_color'    => '#87909e',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          => 1,
            ],
            [
                'name'          => 'INFO VERIFIED', 
                'description'   => 'info verified',
                'color'         => '#aa8d80',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          => 2,
            ],
            [
                'name'          => 'LABEL PRINTED', 
                'description'   => 'label printed',
                'color'         => '#e16b16',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          => 3,
            ],
            [
                'name'          => 'PHARMACY OUTREACH', 
                'description'   => 'pharmacy outreach',
                'color'         => '#1290e0',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          => 4,
            ],
            [
                'name'          => 'PRESCRIBER OUTREACH', 
                'description'   => 'prescriber outreach',
                'color'         => '#ee5e99',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          => 5,
            ],
            [
                'name'          => 'FOR THERAPY CHANGE', 
                'description'   => 'for therapy change',
                'color'         => '#e16b16',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          => 6,
            ],
            [
                'name'          => 'VERBAL PHARMACY', 
                'description'   => 'verbal pharmacy',
                'color'         => '#f8ae00',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          => 7,
            ],
            [
                'name'          => 'VERBAL PRESCRIBER', 
                'description'   => 'verbal prescriber',
                'color'         => '#f8ae00',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          => 8,
            ],
            [
                'name'          => 'BRIDGE TELEHEALTH', 
                'description'   => 'bridge telehealth',
                'color'         => '#f8ae00',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          => 9,
            ],
            [
                'name'          => 'FILL PROCESS', 
                'description'   => 'fill process',
                'color'         => '#4466ff',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          => 10,
            ],
            [
                'name'          => 'IOU', 
                'description'   => 'iou',
                'color'         => '#d33d44',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          =>11,
            ],
            [
                'name'          => 'WAITING FOR PICK UP', 
                'description'   => 'waiting for pick up',
                'color'         => '#aa8d80',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          => 12,
            ],
            [
                'name'          => 'SHIPPED', 
                'description'   => 'shipped',
                'color'         => '#0f9d9f',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          => 13,
            ],
            [
                'name'          => 'ISSUE', 
                'description'   => 'issue',
                'color'         => '#d33d44',
                'text_color'    => '#ffffff',
                'class'         => 'btn btn',
                'widget_icon'   => 'fa-solid fa-circle',
                'sort'          => 14,
            ],
            
        ]);
    }
}
