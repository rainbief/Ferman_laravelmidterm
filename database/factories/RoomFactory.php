<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $floor = fake()->numberBetween(1, 10);
        $roomNumber = fake()->unique()->numberBetween(100, 999);
        
        return [
            'room_number' => (string) $roomNumber,
            'floor' => $floor,
            'status' => fake()->randomElement(['available', 'occupied', 'maintenance']),
            'description' => fake()->optional()->sentence(),
            'room_type_id' => function () {
                if (fake()->boolean(80) && RoomType::count() > 0) {
                    return RoomType::inRandomOrder()->first()->id;
                }
                return null;
            },
        ];
    }
}
