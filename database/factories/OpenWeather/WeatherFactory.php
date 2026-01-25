<?php

namespace Database\Factories\OpenWeather;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OpenWeather\Weather>
 */
class WeatherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city_id' => 1,
            'weather_description' => 'погода',
            'main_temp' => 17.22,
            'main_feels_like' => 18,
            'main_pressure' => 1005,
            'main_humidity' => 75,
            'visibility' => 10000,
            'wind_speed' => 3.25,
            'wind_deg' => 180,
            'clouds_all' => 40,
            'created_at' => now(),
        ];
    }
}
