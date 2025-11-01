<?php

namespace Database\Factories\Quiz;

use App\Models\Quiz\QuizItem;
use App\Services\Quiz\Enums\QuizItemStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizItemFactory extends Factory
{
    protected $model = QuizItem::class;
    
    public function definition(): array
    {
        return [
            'description' => 'Описание тестового вопроса',
            'status' => QuizItemStatus::AtWork->value,
        ];
    }
}
