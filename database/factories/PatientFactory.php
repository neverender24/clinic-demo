<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'last_name' => fake()->lastName(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->lastName(),
            'birthday' => fake()->date('Y-m-d', '-18 years'),
            'sex' => fake()->randomElement(['M', 'F']),
            'contact_details' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'civil_status' => fake()->randomElement(['Single', 'Married', 'Widowed', 'Divorced']),
            'occupation' => fake()->jobTitle(),
            'allergies' => null,
            'surgeries' => null,
            'user_id' => User::factory(),
        ];
    }

    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'sex' => 'M',
        ]);
    }

    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'sex' => 'F',
        ]);
    }
}
