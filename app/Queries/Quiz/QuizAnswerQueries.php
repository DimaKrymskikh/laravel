<?php

namespace App\Queries\Quiz;

use App\Models\Quiz\QuizAnswer;
use App\Queries\ModelQueries;

class QuizAnswerQueries extends ModelQueries
{
    protected function setClassName(): void
    {
        $this->className = QuizAnswer::class;
    }
}
