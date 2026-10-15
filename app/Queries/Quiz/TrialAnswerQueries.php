<?php

namespace App\Queries\Quiz;

use App\Models\Quiz\TrialAnswer;
use App\Queries\ModelQueries;

class TrialAnswerQueries extends ModelQueries
{
    protected function setClassName(): void
    {
        $this->className = TrialAnswer::class;
    }
}
