<?php

namespace App\Services\Quiz\Fields\DataTransferObjects;

use App\ValueObjects\ScalarTypes\SimpleStringValue;

final readonly class QuizItemDto
{
    public function __construct(
            public int $quizId,
            public SimpleStringValue $description,
    ) {
    }
}
