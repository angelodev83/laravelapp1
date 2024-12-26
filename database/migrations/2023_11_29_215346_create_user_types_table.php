<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_types', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Insert initial data
        DB::table('user_types')->insert([
            ['name' => 'Customer Service', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ubacare', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_types');
    }
};
