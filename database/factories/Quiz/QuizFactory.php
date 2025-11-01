<?php

namespace Database\Factories\Quiz;

use App\Models\Quiz\Quiz;
use App\Services\Quiz\Enums\QuizStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

final class QuizFactory extends Factory
{
    protected $model = Quiz::class;
    
    public function definition(): array
    {
        return [
            'title' => 'Тестовый опрос',
            'description' => 'Описание тестового опроса',
            'status' => QuizStatus::AtWork->value,
        ];
    }
}
