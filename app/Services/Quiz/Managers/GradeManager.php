<?php

namespace App\Services\Quiz\Managers;

use App\Models\Quiz\Trial;
use App\Services\Quiz\Enums\Grade;

/**
 * Определяет оценку пользователя по данным опроса из таблицы 'quiz.trials'
 */
final class GradeManager
{
    /**
     * Анализирует данные опроса из таблицы 'quiz.trials'.
     * Возвращает оценку пользователя
     * 
     * @param Trial $trial - опрос из таблицы 'quiz.trials'
     * @return string
     */
    public static function find(Trial $trial): string
    {
        $amGrade = $trial->correct_answers_number / $trial->total_quiz_items * 100;
        
        return match (true) {
            $amGrade >= 90 => Grade::Excellent->value,
            $amGrade >= 80 => Grade::Good->value,
            $amGrade >= 60 => Grade::Satisfactory->value,
            default => Grade::Fail->value
        };
    }
}
