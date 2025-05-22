<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Student;
use App\Models\SeatingPlan;
use App\Models\SeatingOverride;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeatingOverrideFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SeatingOverride::class;

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
            'reason' => $this->faker->sentence(),
            'created_by' => $this->faker->name(),
        ];
    }
    
    /**
     * Configure the model factory to create an override with a specific reason.
     *
     * @param string $reason The reason for the override
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withReason($reason)
    {
        return $this->state(function (array $attributes) use ($reason) {
            return [
                'reason' => $reason,
            ];
        });
    }
    
    /**
     * Configure the model factory to create an override for a specific seat.
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
     * Configure the model factory to create an override for a front row seat.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function frontRowSeat()
    {
        return $this->state(function (array $attributes) {
            return [
                'seat_number' => $this->faker->numberBetween(1, 5),
                'reason' => 'Front row accommodation needed. ' . $this->faker->sentence(),
            ];
        });
    }
    
    /**
     * Configure the model factory to create an override for a student with disability.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forDisability()
    {
        return $this->state(function (array $attributes) {
            return [
                'reason' => 'Disability accommodation. ' . $this->faker->sentence(),
            ];
        });
    }
    
    /**
     * Configure the model factory to create an override for a medical reason.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forMedical()
    {
        return $this->state(function (array $attributes) {
            return [
                'reason' => 'Medical accommodation. ' . $this->faker->sentence(),
            ];
        });
    }
    
    /**
     * Configure the model factory to create an override created by an admin.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function createdByAdmin()
    {
        return $this->state(function (array $attributes) {
            return [
                'created_by' => 'Admin User',
            ];
        });
    }
    
    /**
     * Configure the model factory to create an override created by an invigilator.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function createdByInvigilator()
    {
        return $this->state(function (array $attributes) {
            return [
                'created_by' => 'Invigilator User',
            ];
        });
    }
}

