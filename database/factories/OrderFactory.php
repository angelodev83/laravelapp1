<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        
            'patient_id' => \App\Models\Patient::inRandomOrder()->first()->id,
            'order_number' => $this->faker->randomNumber(5),
            'rx_image' => null,
            'intake_form' => null,
            'pod_proof_of_delivery' => null,
            'shipment_type' => $this->faker->sentence,
            'shipment_tracking_number' => $this->faker->randomNumber(8),
            'shipment_status_id' => $this->faker->numberBetween(1,3),
            'created_at' => $this->faker->date('Y-m-d H:i:s'),
            'updated_at' => $this->faker->date('Y-m-d H:i:s'),
            
        ];
    }
}
