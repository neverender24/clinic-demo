<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClinicFactory extends Factory
{
    protected $model = Clinic::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'location' => fake()->address(),
            'header_image' => null,
            'medcert_header_image' => null,
            'watermarks' => null,
        ];
    }
}
