<?php

namespace App\Services\Quiz\Fields;

use App\Exceptions\RuleException;
use App\Models\Quiz\QuizAnswer;
use App\ValueObjects\ScalarTypes\BoolValue;
use App\ValueObjects\ScalarTypes\SimpleStringValue;

final readonly class QuizAnswerField
{
    public string $field;
    public mixed $value;
    
    private function __construct(string|null $field, mixed $value)
    {
        $strField = trim($field ?? '');
        
        if (!in_array($strField, QuizAnswer::EDITABLE_FIELDS)) {
            throw new RuleException('message', sprintf('Либо в таблице "quiz.quiz_answers" нет поля "%s", либо это поле не редактируемое.', $strField));
        }
        
        $this->field = $strField;
        
        $this->getFieldValue($value);
    }
    
    public static function create(string|null $field, mixed $value): self
    {
        return new self($field, $value);
    }
    
    private function getFieldValue(mixed $value): void
    {
        $this->value = match($this->field) {
            'is_correct' => BoolValue::create($value)->value,
            default => SimpleStringValue::create($value)->value,
        };
    }
}
