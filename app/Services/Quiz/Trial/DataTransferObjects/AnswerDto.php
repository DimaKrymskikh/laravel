<?php

namespace App\Services\Quiz\Trial\DataTransferObjects;

use App\Models\User;

/**
 * Данные при записи ответа, который выбрал пользователь при опросе
 */
final readonly class AnswerDto
{
    public function __construct(
            public User $user,
            public int $id, // id таблицы 'quiz.trial_answers'
            public int $quiz_answer_id, // quiz_answer_id таблицы 'quiz.trial_answers'
    ) {
    }
}
