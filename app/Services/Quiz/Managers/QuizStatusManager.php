<?php

namespace App\Services\Quiz\Managers;

use App\Exceptions\RuleException;
use App\Models\Quiz\Quiz;
use App\Services\Quiz\Enums\QuizStatus;

/**
 * Управляет изменением статуса опроса
 */
final class QuizStatusManager
{
    const MESSAGE_ABOUT_STATUS_CHANGE_BY_MANUAL_CONTROL = 'Статус "%s" опроса не может быть изменён ручным управлением.';
    const MESSAGE_DOESNT_CONTAIN = 'Статуса "%s" опроса нет в списке возможных следующих статусов у статуса "%s".';
    
    private QuizStatus $oldStatus;
    private QuizStatus $newStatus;
    private int $nQuizItemsWithStatusReady;
    private int $nQuizItemsWithStatusAtWork;

    public function __construct(Quiz $quiz)
    {
        $this->oldStatus = QuizStatus::from($quiz->status);
        $this->nQuizItemsWithStatusReady = $quiz->quizItems->where('status', QuizStatus::Ready->value)->count();
        $this->nQuizItemsWithStatusAtWork = $quiz->quizItems->where('status', QuizStatus::AtWork->value)->count();
    }
    
    /**
     * Автоматически определяет статус опроса.
     * 
     * @return void
     */
    public function defineNewStatus(): void
    {
        if ($this->nQuizItemsWithStatusReady >= Quiz::MINIMUM_ITEMS_FOR_READY_STATUS && $this->nQuizItemsWithStatusAtWork === 0) {
            $this->newStatus = QuizStatus::Ready;
        } else {
            $this->newStatus =  QuizStatus::AtWork;
        }
    }
    
    /**
     * Проверяет возможность установления нового статуса опроса при ручном управлении.
     * И, если это возможно, сохраняет этот новый статус.
     * 
     * @param QuizStatus $newStatus
     * @return void
     * @throws RuleException
     */
    public function approveNewStatus(QuizStatus $newStatus): void
    {
        if (collect(QuizStatus::AUTOMATIC_STATUSES)->contains($newStatus)) {
            throw new RuleException('message', sprintf(self::MESSAGE_ABOUT_STATUS_CHANGE_BY_MANUAL_CONTROL, $newStatus->getInfo()->name));
        }
        
        if ($this->oldStatus->getInfo()->getNextStatuses()->doesntContain($newStatus)) {
            throw new RuleException('message', sprintf(self::MESSAGE_DOESNT_CONTAIN, $newStatus->getInfo()->name, $this->oldStatus->getInfo()->name));
        }
        
        $this->newStatus =  $newStatus;
    }
    
    /**
     * Проверяет, изменился ли статус опроса.
     * 
     * @return bool
     */
    public function checkOldAndNewStatusAreNotEqual(): bool
    {
        return $this->newStatus !== $this->oldStatus;
    }
    
    /**
     * Возвращает величину нового статуса опроса
     * 
     * @return string
     */
    public function getNewStatusValue(): string
    {
        return $this->newStatus->value;
    }
}
