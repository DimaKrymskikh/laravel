<?php

namespace Database\Factories\Person;

use App\Models\Person\UserFilm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person\UserFilm>
 */
class UserFilmFactory extends Factory
{
    protected $model = UserFilm::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'film_id' => 7
        ];
    }
}
