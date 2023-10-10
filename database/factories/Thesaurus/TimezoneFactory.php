<?php

namespace Database\Factories\Thesaurus;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Thesaurus\Timezone>
 */
class TimezoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Asia/Krasnoyarsk',
        ];
    }
}
