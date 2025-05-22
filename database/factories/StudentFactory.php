<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $years = [1, 2, 3, 4];
        $sections = ['A', 'B', 'C', 'D'];
        
        return [
            'name' => $this->faker->name(),
            'roll_number' => $this->faker->unique()->bothify('R##???###'),
            'course_id' => Course::factory(),
            'year' => $this->faker->randomElement($years),
            'section' => $this->faker->randomElement($sections),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'has_disability' => $this->faker->boolean(5), // 5% chance of having a disability
            'is_active' => $this->faker->boolean(95), // 95% chance of being active
        ];
    }
    
    /**
     * Configure the model factory to create a student with a disability.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withDisability()
    {
        return $this->state(function (array $attributes) {
            return [
                'has_disability' => true,
            ];
        });
    }
    
    /**
     * Configure the model factory to create a student without a disability.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withoutDisability()
    {
        return $this->state(function (array $attributes) {
            return [
                'has_disability' => false,
            ];
        });
    }
    
    /**
     * Configure the model factory to create a first-year student.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function firstYear()
    {
        return $this->state(function (array $attributes) {
            return [
                'year' => 1,
            ];
        });
    }
    
    /**
     * Configure the model factory to create a second-year student.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function secondYear()
    {
        return $this->state(function (array $attributes) {
            return [
                'year' => 2,
            ];
        });
    }
    
    /**
     * Configure the model factory to create a third-year student.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function thirdYear()
    {
        return $this->state(function (array $attributes) {
            return [
                'year' => 3,
            ];
        });
    }
    
    /**
     * Configure the model factory to create a fourth-year student.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function fourthYear()
    {
        return $this->state(function (array $attributes) {
            return [
                'year' => 4,
            ];
        });
    }
    
    /**
     * Configure the model factory to create an active student.
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
     * Configure the model factory to create an inactive student.
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

