<?php

namespace Database\Factories\Thesaurus;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Thesaurus\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'TestCity',
            'open_weather_id' => 1,
            'timezone_id' => null
        ];
    }
}
