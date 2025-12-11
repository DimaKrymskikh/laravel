<?php

namespace App\Queries\Quiz\TrialAnswers;

use App\Models\Quiz\TrialAnswer;
use App\Queries\QueriesInterface;

interface TrialAnswerQueriesInterface extends QueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'quiz.trial_answers' нет записи с id=%d";
    
    /**
     * Получить из таблицы 'quiz.trial_answers' запись с первичным ключом id
     * 
     * @param int $id - первичный ключ таблицы 'quiz.trial_answers'
     * @return TrialAnswer
     */
    public function getById(int $id): TrialAnswer;
}
