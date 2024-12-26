<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::truncate();
        // Creating Super Admin User
        $superAdmin = User::create([
            'name' => 'SUPERADMIN', 
            'email' => 'superadmin@tinrx.com',
            'password' => Hash::make('superadmin'),
            'role_id' => 1,
            'type_id' => 1,
        ]);
        $superAdmin->assignRole('super-admin');

        // Creating Super Admin User
        $superAdmin = User::create([
            'name' => 'admin', 
            'email' => 'admin@tinrx.com',
            'password' => Hash::make('admin'),
            'role_id' => 2,
            'type_id' => 1,
        ]);
        $superAdmin->assignRole('super-admin');

        $accountant = User::create([
            'name' => 'Accountant', 
            'email' => 'accountant@tinrx.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
            'type_id' => 1,
        ]);
        $accountant->assignRole('accountant');

        $hr = User::create([
            'name' => 'Human Resource', 
            'email' => 'hr@tinrx.com',
            'password' => Hash::make('password'),
            'role_id' => 4,
            'type_id' => 1,
        ]);
        $hr->assignRole('human-resource');

        $doctor = User::create([
            'name' => 'Pharmacist', 
            'email' => 'pharmacist@tinrx.com',
            'password' => Hash::make('password'),
            'role_id' => 5,
            'type_id' => 1,
        ]);
        $doctor->assignRole('pharmacist');
    }
}
