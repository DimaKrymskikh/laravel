<?php

namespace App\Queries\Quiz\QuizAnswers;

use App\Exceptions\DatabaseException;
use App\Models\Quiz\QuizAnswer;
use App\Support\Collections\Quiz\QuizAnswerCollection;

abstract class QuizAnswerQueries implements QuizAnswerQueriesInterface
{
    public function exists(int $id): bool
    {
        return QuizAnswer::where('id', $id)->exists();
    }
    
    public function getById(int $id): QuizAnswer
    {
        return QuizAnswer::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    public function getList(): QuizAnswerCollection
    {
        return QuizAnswer::all();
    }
}
