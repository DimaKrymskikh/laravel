<?php

namespace Database\Factories\Quiz;

use App\Models\Quiz\QuizAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz\QuizAnswer>
 */
class QuizAnswerFactory extends Factory
{
    protected $model = QuizAnswer::class;
    
    public function definition(): array
    {
        return [
            'description' => 'Текст тестового вопроса',
            'is_correct' => false,
        ];
    }
}
