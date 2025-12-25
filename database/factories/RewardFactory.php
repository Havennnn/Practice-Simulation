<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reward>
 */
class RewardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->bothify('???-#####')),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'points' => fake()->numberBetween(100, 1000),
            'logo_file_id' => null,
        ];
    }
}
