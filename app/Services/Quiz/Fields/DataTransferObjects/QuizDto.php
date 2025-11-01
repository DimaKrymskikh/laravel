<?php

namespace App\Services\Quiz\Fields\DataTransferObjects;

use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizLeadTimeValue;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizTitleValue;
use App\ValueObjects\ScalarTypes\SimpleStringValue;

/**
 * Валидация при создании опроса.
 */
final readonly class QuizDto
{
    public function __construct(
            public QuizTitleValue $title,
            public SimpleStringValue $description,
            public QuizLeadTimeValue $leadTime,
    ) {
    }
}
