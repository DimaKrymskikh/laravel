<?php

namespace App\Queries\Quiz\QuizAnswers;

use App\Models\Quiz\QuizAnswer;
use App\Queries\QueriesInterface;

interface QuizAnswerQueriesInterface extends QueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'quiz.quiz_answers' нет записи с id=%d";
    
    /**
     * Получить из таблицы 'quiz.quiz_answers' запись с первичным ключом id
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quiz_answers'
     * @return QuizAnswer
     */
    public function getById(int $id): QuizAnswer;
}
