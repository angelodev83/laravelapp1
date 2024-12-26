<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ticket_status')->insert([
            [
                'name'          => 'To Do', 
                'description'   => 'Default Status on create',
                'color'         => 'secondary',
                'class'         => 'secondary',
                'sort'          => 1,
            ],
            [
                'name'          => 'In Progress', 
                'description'   => 'The ticket is in progress',
                'color'         => 'primary',
                'class'         => 'primary',
                'sort'          => 2,
            ],
            [
                'name'          => 'To Analyze', 
                'description'   => 'Needs analyzation',
                'color'         => 'info',
                'class'         => 'info',
                'sort'          => 3,
            ],
            [
                'name'          => 'To Verify', 
                'description'   => 'Needs verification',
                'color'         => 'warning',
                'class'         => 'warning',
                'sort'          => 4,
            ],
            [
                'name'          => 'Waiting', 
                'description'   => 'Waiting for someone or something to be done first',
                'color'         => 'danger',
                'class'         => 'danger',
                'sort'          => 5,
            ],
            [
                'name'          => 'Completed', 
                'description'   => 'Ticket is done',
                'color'         => 'success',
                'class'         => 'success',
                'sort'          => 6,
            ]
        ]);
    }
}
