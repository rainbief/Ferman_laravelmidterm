<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomType>
 */
class RoomTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['Single', 'Double', 'Twin', 'Suite', 'Deluxe', 'Executive', 'Presidential', 'Family'];
        
        return [
            'name' => fake()->unique()->randomElement($types) . ' Room',
            'description' => fake()->optional()->sentence(),
            'price_per_night' => fake()->randomFloat(2, 50, 500),
            'max_occupancy' => fake()->numberBetween(1, 6),
        ];
    }
}
