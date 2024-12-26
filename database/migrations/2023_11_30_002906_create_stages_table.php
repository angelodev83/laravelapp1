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
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        // Insert initial data
        DB::table('stages')->insert([
            ['name' => 'Pending', 'description' => 'Pending Stage', 'color' => 'pink', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'In-progress', 'description' => 'In-progress Stage', 'color' => 'yellow', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Uploaded', 'description' => 'Uploaded Stage', 'color' => 'blue', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};

