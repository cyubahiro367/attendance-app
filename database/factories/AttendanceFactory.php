<?php

namespace Database\Factories;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "employeeID" => Employee::inRandomOrder()->first()->id,
            "type" => $this->faker->randomElement([1, 2]),
            "date" => Carbon::parse("2023-03-02")->setHour(0)->setMinute(0)->getTimestamp(),
            "time" => "06:12",
            'userID' => null
        ];
    }
}
