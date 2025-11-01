<?php

namespace App\Services\Quiz\Fields\ValueObjects\Quiz;

use App\Exceptions\RuleException;

final readonly class QuizTitleValue {
    public string $value;
    
    private function __construct(string|null $title)
    {
        $strTitle = trim($title ?? '');
        
        if (!$strTitle) {
            $this->value = $strTitle;
            throw new RuleException('title', 'Название опроса не может быть пустым.');
        }
        
        if (mb_strlen($strTitle) < 10) {
            $this->value = $strTitle;
            throw new RuleException('title', 'Название опроса должно состоять хотя бы из 10 символов.');
        }
        
        if (mb_strlen($strTitle) > 100) {
            $this->value = $strTitle;
            throw new RuleException('title', 'Название опроса не должно привышать 100 символов.');
        }
        
        $this->value = $strTitle;
    }
    
    public static function create(string|null $title): self
    {
        return new self($title);
    }
}
