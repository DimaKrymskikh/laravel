<?php

namespace App\Services\Quiz\Fields\DataTransferObjects;

use App\ValueObjects\ScalarTypes\BoolValue;
use App\ValueObjects\ScalarTypes\SimpleStringValue;

final readonly class QuizAnswerDto 
{
    public function __construct(
            public int $quizItemId,
            public SimpleStringValue $description,
            public BoolValue $isCorrect,
    ) {
    }
}
