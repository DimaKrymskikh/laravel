<?php

namespace App\Services\Quiz\Fields;

use App\Exceptions\RuleException;
use App\Models\Quiz\QuizItem;
use App\ValueObjects\IntValue;
use App\ValueObjects\ScalarTypes\SimpleStringValue;

/**
 * Класс хранит редактируемое поле таблицы 'quiz.quiz_items' и его валидное значение
 * 
 * @property string $field - редактируемое поле таблицы 'quiz.quiz_items'
 * @property IntValue|SimpleStringValue $value - валидное значение поля $field
 */
final readonly class QuizItemField
{
    public string $field;
    public IntValue|SimpleStringValue $value;
    
    private function __construct(string|null $field, mixed $value)
    {
        $strField = trim($field ?? '');
        
        if (!in_array($strField, QuizItem::EDITABLE_FIELDS)) {
            throw new RuleException('message', sprintf('Либо в таблице "quiz.quiz_items" нет поля "%s", либо это поле не редактируемое.', $strField));
        }
        
        $this->field = $strField;
        
        $this->getFieldValue($value);
    }
    
    /**
     * Получает поле $field и его возможное значение $value.
     * Проверяет, что эти величины валидные
     * 
     * @param string|null $field - поле
     * @param mixed $value - возможное значение поля
     * @return self
     */
    public static function create(string|null $field, mixed $value): self
    {
        return new self($field, $value);
    }
    
    private function getFieldValue(mixed $value): void
    {
        $this->value = match($this->field) {
            'priority' => IntValue::create($value),
            default => SimpleStringValue::create($value),
        };
    }
}
