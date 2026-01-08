<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Consultation;
use App\Enums\Enums\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsultationFactory extends Factory
{
    protected $model = Consultation::class;

    public function definition(): array
    {
        return [
            'date' => fake()->date(),
            'chief_complaint' => fake()->sentence(),
            'test_results' => fake()->paragraph(),
            'diagnosis' => fake()->sentence(),
            'management' => fake()->paragraph(),
            'status' => fake()->randomElement([Status::Pending, Status::Done]),
            'clinic_id' => Clinic::factory(),
            'patient_id' => Patient::factory(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Status::Pending,
        ]);
    }

    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Status::Done,
        ]);
    }

    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => now()->toDateString(),
        ]);
    }
}
