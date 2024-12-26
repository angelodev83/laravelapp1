<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        // Insert initial data into the 'status' table
        DB::table('statuses')->insert([
            [
                'name' => 'Done',
                'description' => 'Green',
                'color' => 'green',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Telemed Bridge',
                'description' => 'Light Blue',
                'color' => 'light blue',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '1st Prescriber Outreach',
                'description' => 'Light Blue',
                'color' => 'light blue',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2nd Prescriber Outreach',
                'description' => 'Light Blue',
                'color' => 'light blue',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '3rd Prescriber Outreach',
                'description' => 'Light Blue',
                'color' => 'light blue',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Outreach Cooling Period',
                'description' => 'Yellow',
                'color' => 'yellow',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Failed Prescriber Outreach',
                'description' => 'Red',
                'color' => 'red',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status');
    }
};
