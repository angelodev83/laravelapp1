<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $absolute_path = str_replace('\\', '/' , public_path());

        DB::statement('TRUNCATE table employees');
        DB::table('employees')->insert([
            //on oig hit lists
            [
                'firstname' => 'Super',
                'lastname' => 'Admin',
                'email' => 'superadmin@tinrx.com',
                'position' => 'Super Admin',
                'location' => '',
                'user_id' => '1',
                
            ],
        ]);

        //  DB::statement('LOAD DATA INFILE "'.$absolute_path.'/active_roster.csv"
        //     INTO TABLE employees
        //     FIELDS TERMINATED BY \',\'
        //     ENCLOSED BY \'"\'
        //     LINES TERMINATED BY \'\n\'
        //     IGNORE 1 ROWS
        //     (firstname,lastname,position,email,status,location)');


        // $faker = Factory::create();
        // DB::table('employees')->insert([
        //     //on oig hit lists
        //     [
        //         'firstname' => 'Anita',
        //         'lastname' => 'Wright',
        //         'email' => $faker->unique()->email(),
        //         'position' => $faker->jobTitle(),
        //         'location' => $faker->state(),
        //         'start_date' => Carbon::now()->toDateTimeString(),
        //     ],
        //     [
        //         'firstname' => 'Kimberly',
        //         'lastname' => 'Zupon',
        //         'email' => $faker->unique()->email(),
        //         'position' => $faker->jobTitle(),
        //         'location' => $faker->state(),
        //         'start_date' => Carbon::now()->toDateTimeString(),
        //     ],
        //     [
        //         'firstname' => 'Louis',
        //         'lastname' => 'Glenn',
        //         'email' => $faker->unique()->email(),
        //         'position' => $faker->jobTitle(),
        //         'location' => $faker->state(),
        //         'start_date' => Carbon::now()->toDateTimeString(),
        //     ],
        //     [
        //         'firstname' => 'Janice',
        //         'lastname' => 'Dacosta',
        //         'email' => $faker->unique()->email(),
        //         'position' => $faker->jobTitle(),
        //         'location' => $faker->state(),
        //         'start_date' => Carbon::now()->toDateTimeString(),
        //     ],
        //     [
        //         'firstname' => 'Adam',
        //         'lastname' => 'Cooley',
        //         'email' => $faker->unique()->email(),
        //         'position' => $faker->jobTitle(),
        //         'location' => $faker->state(),
        //         'start_date' => Carbon::now()->toDateTimeString(),
        //     ],
        // ]);



        //\App\Models\Employee::factory(50)->create();
    }
}
