<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuickLinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        DB::table('quick_links')->truncate();
        DB::table('quick_links')->insert([
            [
                'name' => 'My Tasks',
                'bg_color' => '#c2f4f5 !important',
                'txt_color' => '#0d677c !important',
                'icon' => 'fa-regular fa-folder',
                'sort' => 1,
                'link' => '/store/bulletin/{{request()->id}}/task-reminders',
            ],
            [
                'name' => 'Escalations',
                'bg_color' => '#c7caff !important',
                'txt_color' => '#363a86 !important',
                'icon' => 'fa-solid fa-circle-exclamation',
                'sort' => 2,
                'link' => '/store/escalation/{{request()->id}}/tickets',
            ],
            [
                'name' => 'New Drug Order',
                'bg_color' => '#fedcb1 !important',
                'txt_color' => '#a46c26 !important',
                'icon' => 'fa-solid fa-store',
                'sort' => 3,
                'link' => '#',
            ],
            [
                'name' => 'Talk to Pilli Boy AI',
                'bg_color' => '#c2f4f5 !important',
                'txt_color' => '#0d677c !important',
                'icon' => 'fa-solid fa-robot',
                'sort' => 4,
                'link' => '/admin/chatbox',
            ],
            [
                'name' => 'News & Events',
                'bg_color' => '#c7caff !important',
                'txt_color' => '#363a86 !important',
                'icon' => 'fa-regular fa-heart',
                'sort' => 5,
                'link' => '#',
            ],
            [
                'name' => 'For Shipping Today',
                'bg_color' => '#c8f4d2 !important',
                'txt_color' => '#34623d !important',
                'icon' => 'fa-solid fa-truck-arrow-right',
                'sort' => 6,
                'link' => '/store/operations/{{request()->id}}/for-shipping-today',
            ],
            
        ]);
    }
}
