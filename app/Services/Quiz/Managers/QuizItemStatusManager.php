<?php

namespace App\Services\Quiz\Managers;

use App\Exceptions\RuleException;
use App\Models\Quiz\QuizItem;
use App\Services\Quiz\Enums\QuizItemStatus;

final class QuizItemStatusManager
{
    const MESSAGE_ABOUT_STATUS_CHANGE_BY_MANUAL_CONTROL = 'Статус "%s" вопроса не может быть изменён ручным управлением';
    const MESSAGE_DOESNT_CONTAIN = 'Статуса "%s" вопроса нет в списке возможных следующих статусов у статуса "%s"';
    
    private QuizItemStatus $oldStatus;
    private QuizItemStatus $newStatus;
    private int $nAnswers;
    private bool $isCorrectAnswer;

    public function __construct(QuizItem $quizItem)
    {
        $this->oldStatus = QuizItemStatus::from($quizItem->status);
        $this->nAnswers = $quizItem->answers->count();
        $this->isCorrectAnswer = $quizItem->answers->contains('is_correct', true);
    }
    
    /**
     * Задаёт новый статус вопроса в автоматическом режиме.
     * 
     * @return void
     */
    public function defineNewStatus(): void
    {
        if ($this->nAnswers >= QuizItem::MINIMUM_ANSWERS_FOR_READY_STATUS && $this->isCorrectAnswer) {
            $this->newStatus = QuizItemStatus::Ready;
        } else {
            $this->newStatus =  QuizItemStatus::AtWork;
        }
    }
    
    /**
     * Проверяет, что новый статус вопроса $newStatus может быть установлен.
     * 
     * @param QuizItemStatus $newStatus
     * @return void
     * @throws RuleException
     */
    public function approveNewStatus(QuizItemStatus $newStatus): void
    {
        if (collect(QuizItemStatus::AUTOMATIC_STATUSES)->contains($newStatus)) {
            throw new RuleException('message', sprintf(self::MESSAGE_ABOUT_STATUS_CHANGE_BY_MANUAL_CONTROL, $newStatus->getInfo()->name));
        }
        
        if ($this->oldStatus->getInfo()->getNextStatuses()->doesntContain($newStatus)) {
            throw new RuleException('message', sprintf(self::MESSAGE_DOESNT_CONTAIN, $newStatus->getInfo()->name, $this->oldStatus->getInfo()->name));
        }
        
        $this->newStatus =  $newStatus;
    }
    
    /**
     * Проверяет, изменился ли статус вопроса.
     * 
     * @return bool
     */
    public function checkOldAndNewStatusAreNotEqual(): bool
    {
        return $this->newStatus !== $this->oldStatus;
    }
    
    public function getNewStatusValue(): string
    {
        return $this->newStatus->value;
    }
}
