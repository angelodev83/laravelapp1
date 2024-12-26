<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->default('lightblue');
            $table->string('text_color')->default('black');
            $table->timestamps();
        });

        // Insert initial data
        DB::table('request_types')->insert([
            ['name' => 'ERX Orders', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fulfillment Orders', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Telehealth', 'created_at' => now(), 'updated_at' => now()],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_types');
    }
};
