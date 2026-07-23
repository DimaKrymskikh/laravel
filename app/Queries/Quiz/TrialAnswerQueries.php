<?php

namespace App\Queries\Quiz;

use App\Models\Quiz\TrialAnswer;
use App\Queries\QueriesInterface;
use App\Support\Collections\Quiz\TrialAnswerCollection;

class TrialAnswerQueries implements QueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'quiz.trial_answers' нет записи с id=%d";
    
    /**
     * {@inheritDoc} (таблица 'quiz.trial_answers')
     * 
     * @param int $id - первичный ключ таблицы
     * @return bool
     */
    public function exists(int $id): bool
    {
        return TrialAnswer::where('id', $id)->exists();
    }
    
    /**
     * {@inheritDoc} (таблица 'quiz.trial_answers')
     * 
     * @param int $id - первичный ключ таблицы 'quiz.trial_answers'
     * @return TrialAnswer
     */
    public function getById(int $id): TrialAnswer
    {
        return TrialAnswer::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    /**
     * {@inheritDoc} таблицы 'quiz.trial_answers'
     * 
     * @return TrialAnswerCollection
     */
    public function getList(): TrialAnswerCollection
    {
        return TrialAnswer::orderBy('priority')->get();
    }
}
