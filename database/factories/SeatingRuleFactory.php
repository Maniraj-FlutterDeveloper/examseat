<?php

namespace Database\Factories;

use App\Models\SeatingRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeatingRuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SeatingRule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $types = ['alternate_courses', 'distance', 'priority'];
        $type = $this->faker->randomElement($types);
        
        // Define parameters based on rule type
        $parameters = [];
        switch ($type) {
            case 'alternate_courses':
                $parameters = [
                    'min_distance' => $this->faker->numberBetween(1, 3),
                ];
                break;
            case 'distance':
                $parameters = [
                    'distance' => $this->faker->numberBetween(1, 3),
                ];
                break;
            case 'priority':
                $parameters = [
                    'seats_per_row' => $this->faker->numberBetween(4, 8),
                    'door_seat' => $this->faker->numberBetween(1, 5),
                    'accessible_seats' => [$this->faker->numberBetween(10, 15), $this->faker->numberBetween(16, 20)],
                ];
                break;
        }
        
        return [
            'name' => $this->faker->words(3, true) . ' Rule',
            'type' => $type,
            'description' => $this->faker->sentence(),
            'parameters' => $parameters,
            'priority' => $this->faker->numberBetween(1, 10),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
        ];
    }
    
    /**
     * Configure the model factory to create an alternate courses rule.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function alternateCoursesRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'alternate_courses',
                'parameters' => [
                    'min_distance' => $this->faker->numberBetween(1, 3),
                ],
            ];
        });
    }
    
    /**
     * Configure the model factory to create a distance rule.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function distanceRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'distance',
                'parameters' => [
                    'distance' => $this->faker->numberBetween(1, 3),
                ],
            ];
        });
    }
    
    /**
     * Configure the model factory to create a priority rule.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function priorityRule()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'priority',
                'parameters' => [
                    'seats_per_row' => $this->faker->numberBetween(4, 8),
                    'door_seat' => $this->faker->numberBetween(1, 5),
                    'accessible_seats' => [$this->faker->numberBetween(10, 15), $this->faker->numberBetween(16, 20)],
                ],
            ];
        });
    }
    
    /**
     * Configure the model factory to create an active rule.
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
     * Configure the model factory to create an inactive rule.
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

