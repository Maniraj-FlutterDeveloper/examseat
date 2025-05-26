<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $seatsPerRow = $this->faker->numberBetween(4, 8);
        $rows = $this->faker->numberBetween(4, 10);
        $capacity = $seatsPerRow * $rows;
        
        return [
            'block_id' => Block::factory(),
            'room_number' => $this->faker->unique()->bothify('R-###'),
            'capacity' => $capacity,
            'layout' => [
                'seats_per_row' => $seatsPerRow,
                'rows' => $rows,
                'has_door_left' => $this->faker->boolean(),
                'has_door_right' => $this->faker->boolean(),
            ],
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }
    
    /**
     * Configure the model factory to create a small room.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function small()
    {
        return $this->state(function (array $attributes) {
            $seatsPerRow = $this->faker->numberBetween(3, 5);
            $rows = $this->faker->numberBetween(3, 5);
            $capacity = $seatsPerRow * $rows;
            
            return [
                'capacity' => $capacity,
                'layout' => [
                    'seats_per_row' => $seatsPerRow,
                    'rows' => $rows,
                    'has_door_left' => $this->faker->boolean(),
                    'has_door_right' => $this->faker->boolean(),
                ],
            ];
        });
    }
    
    /**
     * Configure the model factory to create a medium room.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function medium()
    {
        return $this->state(function (array $attributes) {
            $seatsPerRow = $this->faker->numberBetween(5, 8);
            $rows = $this->faker->numberBetween(5, 8);
            $capacity = $seatsPerRow * $rows;
            
            return [
                'capacity' => $capacity,
                'layout' => [
                    'seats_per_row' => $seatsPerRow,
                    'rows' => $rows,
                    'has_door_left' => $this->faker->boolean(),
                    'has_door_right' => $this->faker->boolean(),
                ],
            ];
        });
    }
    
    /**
     * Configure the model factory to create a large room.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function large()
    {
        return $this->state(function (array $attributes) {
            $seatsPerRow = $this->faker->numberBetween(8, 12);
            $rows = $this->faker->numberBetween(8, 12);
            $capacity = $seatsPerRow * $rows;
            
            return [
                'capacity' => $capacity,
                'layout' => [
                    'seats_per_row' => $seatsPerRow,
                    'rows' => $rows,
                    'has_door_left' => $this->faker->boolean(),
                    'has_door_right' => $this->faker->boolean(),
                ],
            ];
        });
    }
    
    /**
     * Configure the model factory to create an active room.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }
    
    /**
     * Configure the model factory to create an inactive room.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}

