<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class JobApplicationFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->unique()->name(),
            'email' => fake()->unique()->safeEmail(),
            'job_id' => fake()->randomNumber(),
            'stage_id' =>fake()->randomNumber()
        ];
    }

}
