<?php

namespace Database\Factories\Quiz;

use App\Models\Quiz\Trial;
use Database\Seeders\Tests\Person\UserSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz\Trial>
 */
class TrialFactory extends Factory
{
    protected $model = Trial::class;
    
    public function definition(): array
    {
        return [
            'user_id' => UserSeeder::ID_TEST_LOGIN
         ];
    }
}
