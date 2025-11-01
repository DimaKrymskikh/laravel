<?php

namespace App\Queries\Quiz\QuizItems;

use App\Models\Quiz\QuizItem;
use App\Queries\QueriesInterface;

interface QuizItemQueriesInterface extends QueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'quiz.quiz_items' нет записи с id=%d";
    
    public function getByIdWithAnswers(int $id): QuizItem;
}
