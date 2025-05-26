<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $courses = [
            'B.Tech Computer Science',
            'B.Tech Electronics',
            'B.Tech Mechanical',
            'B.Tech Civil',
            'B.Tech Electrical',
            'BBA',
            'MBA',
            'B.Com',
            'M.Com',
            'BCA',
            'MCA',
            'B.Sc Physics',
            'B.Sc Chemistry',
            'B.Sc Mathematics',
            'M.Sc Physics',
            'M.Sc Chemistry',
            'M.Sc Mathematics',
        ];
        
        return [
            'course_name' => $this->faker->unique()->randomElement($courses),
            'course_code' => $this->faker->unique()->bothify('C###'),
            'department' => $this->faker->randomElement(['Engineering', 'Science', 'Commerce', 'Arts', 'Management']),
            'duration_years' => $this->faker->randomElement([2, 3, 4, 5]),
            'is_active' => $this->faker->boolean(95), // 95% chance of being active
        ];
    }
    
    /**
     * Configure the model factory to create an engineering course.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function engineering()
    {
        return $this->state(function (array $attributes) {
            $courses = [
                'B.Tech Computer Science',
                'B.Tech Electronics',
                'B.Tech Mechanical',
                'B.Tech Civil',
                'B.Tech Electrical',
                'M.Tech Computer Science',
                'M.Tech Electronics',
            ];
            
            return [
                'course_name' => $this->faker->unique()->randomElement($courses),
                'department' => 'Engineering',
                'duration_years' => 4,
            ];
        });
    }
    
    /**
     * Configure the model factory to create a science course.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function science()
    {
        return $this->state(function (array $attributes) {
            $courses = [
                'B.Sc Physics',
                'B.Sc Chemistry',
                'B.Sc Mathematics',
                'B.Sc Biology',
                'M.Sc Physics',
                'M.Sc Chemistry',
                'M.Sc Mathematics',
            ];
            
            return [
                'course_name' => $this->faker->unique()->randomElement($courses),
                'department' => 'Science',
                'duration_years' => $this->faker->randomElement([3, 2]),
            ];
        });
    }
    
    /**
     * Configure the model factory to create a commerce course.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function commerce()
    {
        return $this->state(function (array $attributes) {
            $courses = [
                'B.Com',
                'M.Com',
                'B.Com Accounting',
                'B.Com Finance',
            ];
            
            return [
                'course_name' => $this->faker->unique()->randomElement($courses),
                'department' => 'Commerce',
                'duration_years' => 3,
            ];
        });
    }
    
    /**
     * Configure the model factory to create a management course.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function management()
    {
        return $this->state(function (array $attributes) {
            $courses = [
                'BBA',
                'MBA',
                'BBA Marketing',
                'BBA Finance',
                'MBA Marketing',
                'MBA Finance',
                'MBA HR',
            ];
            
            return [
                'course_name' => $this->faker->unique()->randomElement($courses),
                'department' => 'Management',
                'duration_years' => $this->faker->randomElement([2, 3]),
            ];
        });
    }
    
    /**
     * Configure the model factory to create an active course.
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
     * Configure the model factory to create an inactive course.
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

