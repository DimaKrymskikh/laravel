<?php

namespace App\Services\Quiz\Enums\ValueObjects;

use App\Exceptions\RuleException;
use App\Services\Quiz\Enums\QuizItemStatus;

final readonly class QuizItemStatusValue
{
    public QuizItemStatus $status;
    
    private function __construct(string|null $status)
    {
        $strStatus = trim($status ?? '');
        $this->status = QuizItemStatus::tryFrom($strStatus) ?? throw new RuleException('message', sprintf(QuizItemStatus::MESSAGE_NOT_STATUS_VALUE, $status));
    }
    
    public static function create(string|null $status): self
    {
        return new self($status);
    }
    
    /**
     * Проверяет, что статус вопроса можно изменить ручным управление
     * 
     * @return void
     * @throws RuleException
     */
    public function allowQuizItemChanges(): void
    {
        if (collect(QuizItemStatus::MANUAL_CONTROL_STATUSES)->contains($this->status)) {
            throw new RuleException('message', sprintf(QuizItemStatus::MESSAGE_FINAL_STATUS_NOT_EDITABLE, $this->status->getInfo()->name));
        }
    }
    
    /**
     * Проверяет, что статус вопроса можно отменить ручным управление
     * 
     * @return void
     * @throws RuleException
     */
    public function checkFinalStatus(): void
    {
        if (collect(QuizItemStatus::MANUAL_CONTROL_STATUSES)->doesntContain($this->status)) {
            throw new RuleException('message', sprintf(QuizItemStatus::MESSAGE_NOT_FINAL_STATUS, $this->status->getInfo()->name));
        }
    }
}
