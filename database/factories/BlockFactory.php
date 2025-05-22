<?php

namespace Database\Factories;

use App\Models\Block;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Block::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'block_name' => $this->faker->unique()->randomElement(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']) . ' Block',
            'location' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }
    
    /**
     * Configure the model factory to create an active block.
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
     * Configure the model factory to create an inactive block.
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
    
    /**
     * Configure the model factory to create a main building block.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function mainBuilding()
    {
        return $this->state(function (array $attributes) {
            return [
                'block_name' => 'Main Building ' . $this->faker->randomElement(['A', 'B', 'C']),
                'location' => 'Main Campus',
            ];
        });
    }
    
    /**
     * Configure the model factory to create an annex block.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function annex()
    {
        return $this->state(function (array $attributes) {
            return [
                'block_name' => 'Annex ' . $this->faker->randomElement(['1', '2', '3']),
                'location' => 'Annex Campus',
            ];
        });
    }
}

