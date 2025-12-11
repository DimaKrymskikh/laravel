<?php

namespace App\Queries\Quiz\QuizAnswers;

use App\Exceptions\DatabaseException;
use App\Models\Quiz\QuizAnswer;
use App\Support\Collections\Quiz\QuizAnswerCollection;

final class QuizAnswerQueries implements QuizAnswerQueriesInterface
{
    /**
     * {@inheritDoc} (таблица 'quiz.quiz_answers')
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quiz_answers'
     * @return bool
     */
    public function exists(int $id): bool
    {
        return QuizAnswer::where('id', $id)->exists();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quiz_answers'
     * @return QuizAnswer
     */
    public function getById(int $id): QuizAnswer
    {
        return QuizAnswer::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    /**
     * {@inheritDoc} 'quiz.quiz_answers'
     * 
     * @return QuizAnswerCollection
     */
    public function getList(): QuizAnswerCollection
    {
        return QuizAnswer::all();
    }
}
