<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Employee;
use App\Models\SupportCategory;
use App\Models\SupportEmployee;

class SupportCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SupportEmployee::truncate();
        SupportCategory::truncate();
        SupportCategory::insertOrIgnore([
            ['id' => 1,'name' => 'IT/Software Support'],
            ['id' => 2,'name' => 'Pharmacy Support'],
            ['id' => 3,'name' => 'Finance'],
        ]);

        $itsoftwareSupport = Employee::where('email','remediosyamat@gmail.com')->first();
        $pharmacySupport = Employee::where('email','hero@tinrx.com')->first();
        $financeSupport = Employee::where('email','zedzedarada@gmail.com')->first();
        
        if(isset($itsoftwareSupport->id)) {
            SupportEmployee::insertOrIgnore([
                ['category_id' => 1, 'employee_id' => $itsoftwareSupport->id, 'is_head_support' => 1]
            ]);
        }
        if(isset($pharmacySupport->id)) {
            SupportEmployee::insertOrIgnore([
                ['category_id' => 2, 'employee_id' => $pharmacySupport->id, 'is_head_support' => 1]
            ]);
        }
        if(isset($financeSupport->id)) {
            SupportEmployee::insertOrIgnore([
                ['category_id' => 3, 'employee_id' => $financeSupport->id, 'is_head_support' => 1]
            ]);
        }
    }
}
