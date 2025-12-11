<?php

namespace App\Queries\Quiz\TrialAnswers;

use App\Models\Quiz\TrialAnswer;
use App\Support\Collections\Quiz\TrialAnswerCollection;

final class TrialAnswerQueries implements TrialAnswerQueriesInterface
{
    public function exists(int $id): bool
    {
        return TrialAnswer::where('id', $id)->exists();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param int $id - первичный ключ таблицы 'quiz.trial_answers'
     * @return TrialAnswer
     */
    public function getById(int $id): TrialAnswer
    {
        return TrialAnswer::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    public function getList(): TrialAnswerCollection
    {
        return TrialAnswer::orderBy('priority')->get();
    }
}
