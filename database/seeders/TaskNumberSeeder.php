<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = Task::whereNull('number')->get();
        foreach($tasks as $t) {
            $t->number = Task::generateNumber();
            $t->save();
        }
    }
}
