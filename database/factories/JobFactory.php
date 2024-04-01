<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\JobType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class JobFactory extends Factory
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
        $start_date = $this->faker->dateTimeBetween('-6 months', '+6 months');
        return [
            'title' => fake()->unique()->jobTitle(),
            'description' => fake()->randomHtml(),
            'department_id' => function () {
                return Department::inRandomOrder()->first()->id;
            },
            'job_type_id' => function () {
                return JobType::inRandomOrder()->first()->id;
            },
            'start_date' => $start_date,
            'end_date' => fake()->dateTimeBetween(
                $start_date->format('Y-m-d H:i:s'),
                $start_date->format('Y-m-d H:i:s').' +30 days'),
        ];
    }

}
