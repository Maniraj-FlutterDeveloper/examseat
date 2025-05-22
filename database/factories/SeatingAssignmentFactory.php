<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Student;
use App\Models\SeatingPlan;
use App\Models\SeatingAssignment;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeatingAssignmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SeatingAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'seating_plan_id' => SeatingPlan::factory(),
            'student_id' => Student::factory(),
            'room_id' => Room::factory(),
            'seat_number' => $this->faker->numberBetween(1, 50),
            'is_override' => $this->faker->boolean(20), // 20% chance of being an override
        ];
    }
    
    /**
     * Configure the model factory to create an assignment for a specific seat.
     *
     * @param int $seatNumber The seat number
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forSeat($seatNumber)
    {
        return $this->state(function (array $attributes) use ($seatNumber) {
            return [
                'seat_number' => $seatNumber,
            ];
        });
    }
    
    /**
     * Configure the model factory to create an assignment that is an override.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function asOverride()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_override' => true,
            ];
        });
    }
    
    /**
     * Configure the model factory to create an assignment that is not an override.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function notOverride()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_override' => false,
            ];
        });
    }
    
    /**
     * Configure the model factory to create a front row assignment.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function frontRow()
    {
        return $this->state(function (array $attributes) {
            return [
                'seat_number' => $this->faker->numberBetween(1, 5),
            ];
        });
    }
    
    /**
     * Configure the model factory to create a middle row assignment.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function middleRow()
    {
        return $this->state(function (array $attributes) {
            return [
                'seat_number' => $this->faker->numberBetween(6, 15),
            ];
        });
    }
    
    /**
     * Configure the model factory to create a back row assignment.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function backRow()
    {
        return $this->state(function (array $attributes) {
            return [
                'seat_number' => $this->faker->numberBetween(16, 30),
            ];
        });
    }
}

