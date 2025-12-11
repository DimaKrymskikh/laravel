<?php

namespace App\Services\Quiz\Fields;

use App\Exceptions\RuleException;
use App\Models\Quiz\Quiz;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizLeadTimeValue;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizTitleValue;
use App\ValueObjects\ScalarTypes\SimpleStringValue;

/**
 * Класс хранит редактируемое поле таблицы 'quiz.quizzes' и его валидное значение
 * 
 * @property string $field - редактируемое поле таблицы 'quiz.quizzes'
 * @property QuizTitleValue|QuizLeadTimeValue|SimpleStringValue $value - валидное значение поля $field
 */
final readonly class QuizField
{
    const FAIL_TITLE_MESSAGE = 'Название опроса "%s" уже существует в базе данных. Внесите изменение в название.';
    
    public string $field;
    public QuizTitleValue|QuizLeadTimeValue|SimpleStringValue $value;

    private function __construct(string|null $field, int|string|null $value)
    {
        $strField = trim($field ?? '');
        
        if(collect(Quiz::EDITABLE_FIELDS)->doesntContain($strField)) {
            throw new RuleException('message', "Поле '$strField' таблицы 'quiz.quizzes' либо не существует, либо не может быть изменено произвольным образом.");
        }
        
        $this->field = $strField;
        
        $this->getFieldValue($value);
    }
    
    /**
     * Получает поле $field и его возможное значение $value.
     * Проверяет, что эти величины валидные
     * 
     * @param string|null $field - поле
     * @param int|string|null $value - возможное значение поля
     * @return self
     */
    public static function create(string|null $field, int|string|null $value): self
    {
        return new self($field, $value);
    }
    
    public function checkFieldUniqueness(bool $isExists): void
    {
        if($this->field === 'title' && $isExists) {
            throw new RuleException('title', sprintf(self::FAIL_TITLE_MESSAGE, $this->value->value));
        }
    }
    
    private function getFieldValue(int|string|null $value): void
    {
        $this->value = match($this->field) {
            'title' => QuizTitleValue::create($value),
            'lead_time' => QuizLeadTimeValue::create($value),
            default => SimpleStringValue::create($value),
        };
    }
}
