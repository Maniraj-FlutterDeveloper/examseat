<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\SeatingPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeatingPlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SeatingPlan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $examDate = $this->faker->dateTimeBetween('+1 week', '+3 months');
        $startTime = $this->faker->dateTimeBetween('09:00', '14:00');
        $endTime = clone $startTime;
        $endTime->modify('+' . $this->faker->numberBetween(2, 3) . ' hours');
        
        $statuses = ['scheduled', 'ready', 'ongoing', 'completed', 'cancelled'];
        
        return [
            'room_id' => Room::factory(),
            'exam_name' => $this->faker->sentence(3) . ' Exam',
            'exam_date' => $examDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $this->faker->randomElement($statuses),
            'notes' => $this->faker->optional(0.7)->paragraph(),
        ];
    }
    
    /**
     * Configure the model factory to create a scheduled seating plan.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function scheduled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'scheduled',
            ];
        });
    }
    
    /**
     * Configure the model factory to create a ready seating plan.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function ready()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'ready',
            ];
        });
    }
    
    /**
     * Configure the model factory to create an ongoing seating plan.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function ongoing()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'ongoing',
                'exam_date' => $this->faker->dateTimeBetween('-1 day', 'today'),
            ];
        });
    }
    
    /**
     * Configure the model factory to create a completed seating plan.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'exam_date' => $this->faker->dateTimeBetween('-3 months', '-1 day'),
            ];
        });
    }
    
    /**
     * Configure the model factory to create a cancelled seating plan.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
            ];
        });
    }
    
    /**
     * Configure the model factory to create a morning exam.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function morningExam()
    {
        return $this->state(function (array $attributes) {
            $startTime = $this->faker->dateTimeBetween('09:00', '11:00');
            $endTime = clone $startTime;
            $endTime->modify('+' . $this->faker->numberBetween(2, 3) . ' hours');
            
            return [
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
        });
    }
    
    /**
     * Configure the model factory to create an afternoon exam.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function afternoonExam()
    {
        return $this->state(function (array $attributes) {
            $startTime = $this->faker->dateTimeBetween('13:00', '15:00');
            $endTime = clone $startTime;
            $endTime->modify('+' . $this->faker->numberBetween(2, 3) . ' hours');
            
            return [
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
        });
    }
}

