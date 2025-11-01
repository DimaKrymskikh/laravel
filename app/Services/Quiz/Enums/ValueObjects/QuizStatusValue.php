<?php

namespace App\Services\Quiz\Enums\ValueObjects;

use App\Exceptions\RuleException;
use App\Services\Quiz\Enums\QuizStatus;

final readonly class QuizStatusValue
{
    public QuizStatus $status;
    
    private function __construct(string|null $status)
    {
        $strStatus = trim($status ?? '');
        $this->status = QuizStatus::tryFrom($strStatus) ?? throw new RuleException('message', sprintf(QuizStatus::MESSAGE_NOT_STATUS_VALUE, $status));
    }
    
    public static function create(string|null $status): self
    {
        return new self($status);
    }
    
    /**
     * Проверяет, что статус опроса можно изменить ручным управление
     * 
     * @return void
     * @throws RuleException
     */
    public function allowQuizChanges(): void
    {
        if (collect(QuizStatus::MANUAL_CONTROL_STATUSES)->contains($this->status)) {
            throw new RuleException('message', sprintf(QuizStatus::MESSAGE_FINAL_STATUS_NOT_EDITABLE, $this->status->getInfo()->name));
        }
    }
    
    /**
     * Проверяет, что статус опроса можно отменить ручным управление
     * 
     * @return void
     * @throws RuleException
     */
    public function checkFinalStatus(): void
    {
        if (collect(QuizStatus::MANUAL_CONTROL_STATUSES)->doesntContain($this->status)) {
            throw new RuleException('message', sprintf(QuizStatus::MESSAGE_NOT_FINAL_STATUS, $this->status->getInfo()->name));
        }
    }
}
