<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\StudentPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentPriorityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StudentPriority::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $types = ['disability', 'medical', 'other'];
        
        return [
            'student_id' => Student::factory(),
            'priority_type' => $this->faker->randomElement($types),
            'priority_level' => $this->faker->numberBetween(1, 10),
            'description' => $this->faker->paragraph(),
            'requirements' => $this->faker->sentences(3, true),
            'valid_until' => $this->faker->optional(0.7)->dateTimeBetween('+1 month', '+1 year'),
            'is_verified' => $this->faker->boolean(70), // 70% chance of being verified
        ];
    }
    
    /**
     * Configure the model factory to create a disability priority.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function disability()
    {
        return $this->state(function (array $attributes) {
            return [
                'priority_type' => 'disability',
                'priority_level' => $this->faker->numberBetween(7, 10), // Higher priority for disabilities
                'requirements' => 'Requires accessible seating. ' . $this->faker->sentence(),
            ];
        });
    }
    
    /**
     * Configure the model factory to create a medical priority.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function medical()
    {
        return $this->state(function (array $attributes) {
            return [
                'priority_type' => 'medical',
                'priority_level' => $this->faker->numberBetween(5, 9),
                'requirements' => 'Requires medical accommodation. ' . $this->faker->sentence(),
            ];
        });
    }
    
    /**
     * Configure the model factory to create a high priority.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function highPriority()
    {
        return $this->state(function (array $attributes) {
            return [
                'priority_level' => $this->faker->numberBetween(8, 10),
            ];
        });
    }
    
    /**
     * Configure the model factory to create a medium priority.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function mediumPriority()
    {
        return $this->state(function (array $attributes) {
            return [
                'priority_level' => $this->faker->numberBetween(4, 7),
            ];
        });
    }
    
    /**
     * Configure the model factory to create a low priority.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function lowPriority()
    {
        return $this->state(function (array $attributes) {
            return [
                'priority_level' => $this->faker->numberBetween(1, 3),
            ];
        });
    }
    
    /**
     * Configure the model factory to create a permanent priority (no expiration).
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function permanent()
    {
        return $this->state(function (array $attributes) {
            return [
                'valid_until' => null,
            ];
        });
    }
    
    /**
     * Configure the model factory to create a verified priority.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function verified()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_verified' => true,
            ];
        });
    }
    
    /**
     * Configure the model factory to create an unverified priority.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_verified' => false,
            ];
        });
    }
}

