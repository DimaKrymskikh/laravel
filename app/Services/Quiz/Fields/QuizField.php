<?php

namespace App\Services\Quiz\Fields;

use App\Exceptions\RuleException;
use App\Models\Quiz\Quiz;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizLeadTimeValue;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizTitleValue;
use App\ValueObjects\ScalarTypes\SimpleStringValue;

final readonly class QuizField
{
    public string $field;
    public mixed $value;
    
    private function __construct(string|null $field, int|string|null $value)
    {
        $strField = trim($field ?? '');
        
        if(collect(Quiz::EDITABLE_FIELDS)->doesntContain($strField)) {
            throw new RuleException('message', "Поле '$strField' таблицы 'quiz.quizzes' либо не существует, либо не может быть изменено произвольным образом.");
        }
        
        $this->field = $strField;
        
        $this->getFieldValue($value);
    }
    
    public static function create(string|null $field, int|string|null $value): self
    {
        return new self($field, $value);
    }
    
    private function getFieldValue(int|string|null $value): void
    {
        $this->value = match($this->field) {
            'title' => QuizTitleValue::create($value)->value,
            'lead_time' => QuizLeadTimeValue::create($value)->value,
            default => SimpleStringValue::create($value)->value,
        };
    }
}
