<?php

namespace Database\Factories\Quiz;

use App\Models\Quiz\TrialAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz\TrialAnswer>
 */
class TrialAnswerFactory extends Factory
{
    protected $model = TrialAnswer::class;
    
    public function definition(): array
    {
        return [
            'question' => 'Текст тестового вопроса, на который отвечает пользователь.',
        ];
    }
}
