<?php

namespace App\Services\Quiz\Fields\ValueObjects\Quiz;

use App\Exceptions\RuleException;
use App\Models\Quiz\Quiz;

final readonly class QuizLeadTimeValue
{
    public int $value; // Время в минутах
    
    private function __construct(int|string|null $leadTime)
    {
        $intLeadTime = intval($leadTime);
        
        if ($intLeadTime <= 0) {
            $this->value = $intLeadTime;
            throw new RuleException('lead_time', 'Время продолжительности опроса должно быть положительным. (Возможно было введено не число.)');
        }
        
        if ($intLeadTime >= Quiz::MAX_LAED_TIME) {
            $this->value = Quiz::DEFAULT_LAED_TIME;
            throw new RuleException('lead_time', 'Задано слишком большое число. Продолжительность опроса не может превышать '.Quiz::MAX_LAED_TIME.' минут.');
        }
        
        $this->value = $intLeadTime;
    }
    
    public static function create(int|string|null $leadTime): self
    {
        return new self($leadTime);
    }
}
