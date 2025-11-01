<?php

namespace App\Queries\Quiz\Quizzes;

use App\Queries\QueriesInterface;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizTitleValue;

interface QuizQueriesInterface extends QueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'quiz.quizzes' нет записи с id=%d";
    
    public function existsByTitle(QuizTitleValue $title, int|null $id = null): bool;
}
