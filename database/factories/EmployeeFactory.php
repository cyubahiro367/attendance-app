<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'names' => $this->faker->firstName." ".$this->faker->lastName,
            'email' => $this->faker->email(),
            'employeeIdentifier' => $this->faker->randomNumber(3,true),
            'phoneNumber' => "25078" . substr($this->faker->unique()->e164PhoneNumber(), 5),
        ];
    }
}
