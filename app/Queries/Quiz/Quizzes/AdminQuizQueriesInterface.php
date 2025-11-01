<?php

namespace App\Queries\Quiz\Quizzes;

use App\Models\Quiz\Quiz;

interface AdminQuizQueriesInterface extends QuizQueriesInterface
{
    public function getQuizByIdWithQuizItems(int $id): Quiz;
}
