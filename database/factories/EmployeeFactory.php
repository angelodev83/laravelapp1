<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lastname' => $this->faker->firstName(),
            'firstname' => $this->faker->lastName(),
            'email' => $this->faker->unique()->email(),
            'position' => $this->faker->jobTitle(),
            'location' => $this->faker->state(),
            'start_date' => Carbon::now()->toDateTimeString(),
            //'eid' => $this->faker->unique()->randomNumber(4),
        ];
    }
}
