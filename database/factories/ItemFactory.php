<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Here is a reference snippet of code from ubacare/database/seeders/ItemSeeder.php:
            // order_id	name	qty	dosage	days_supply	refills_remaining	ndc	rx_stage	rx_status	inventory_type	is_rewrited	created_at	updated_at
            'order_id' => \App\Models\Order::inRandomOrder()->first()->id,
            'name' => 'Test '. $this->faker->word,
            'sig' => 'Test '. $this->faker->word,
            'days_supply' => $this->faker->randomNumber(4),
            'refills_remaining' => $this->faker->randomNumber(4),
            'ndc' => $this->faker->regexify('[0-9]{4}-[0-9]{4}-[0-9]{4}'),
            'rx_stage' => $this->faker->numberBetween(1, 4),
            'rx_status' => $this->faker->numberBetween(1, 4),
            'inventory_type' => $this->faker->randomElement(['RX', '340B']),
            'is_rewrited' => $this->faker->randomNumber(1),
            'created_at' => $this->faker->date('Y-m-d H:i:s'),
            'updated_at' => $this->faker->date('Y-m-d H:i:s'),
        ];
    }
}
