<?php

namespace App\Queries\Quiz\QuizAnswers;

use App\Queries\QueriesInterface;

interface QuizAnswerQueriesInterface extends QueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'quiz.quiz_answers' нет записи с id=%d";
}
