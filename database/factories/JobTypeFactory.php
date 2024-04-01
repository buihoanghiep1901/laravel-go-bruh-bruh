<?php

namespace Database\Factories;


use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class JobTypeFactory extends Factory
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
            'name' => fake()->title(),
        ];
    }

}
