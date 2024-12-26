<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = Ticket::whereNull('code')->get();
        foreach($tickets as $t) {
            $t->code = Ticket::generateUniqueCode();
            $t->save();
        }
    }
}
