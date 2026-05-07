<?php

namespace App\Services\Quiz\Enums;

use App\Exceptions\RuleException;
use App\Services\Quiz\StatusInterface;

/**
 * Статусы вопроса
 */
enum QuizItemStatus: string
{
    case AtWork = 'at_work';
    case Ready = 'ready';
    case Removed = 'removed';
    
    public const AUTOMATIC_STATUSES = [self::AtWork, self::Ready];
    public const MANUAL_CONTROL_STATUSES = [self::Removed];
    public const MESSAGE_NOT_STATUS_VALUE = 'Строка "%s" не может быть значением статуса вопроса.';
    public const MESSAGE_FINAL_STATUS_NOT_EDITABLE = 'Статус вопроса "%s" является финальным, поэтому вопрос и все его составляющие нельзя редактировать.';
    public const MESSAGE_NOT_FINAL_STATUS = 'Статус вопроса "%s" не является финальным.';
    
    /**
     * Возвращает сопутствующую статусу информацию.
     * 
     * @return StatusInterface
     */
    public function getInfo(): StatusInterface
    {
        $status = '\App\Services\Quiz\Enums\QuizItemStatuses\\'.$this->name.'Status';
        
        return new $status();
    }
    
    /**
     * Проверяет, что вопрос можно редактировать
     * 
     * @return true
     * @throws RuleException
     */
    public function allowQuizItemChanges(): true
    {
        return collect(self::AUTOMATIC_STATUSES)->contains($this) ?:
            throw new RuleException('message', sprintf(self::MESSAGE_FINAL_STATUS_NOT_EDITABLE, $this->getInfo()->name));
    }
    
    /**
     * Проверяет, что статус вопроса можно отменить ручным управление
     * 
     * @return true
     * @throws RuleException
     */
    public function checkFinalStatus(): true
    {
        return collect(QuizItemStatus::MANUAL_CONTROL_STATUSES)->contains($this) ?:
            throw new RuleException('message', sprintf(QuizItemStatus::MESSAGE_NOT_FINAL_STATUS, $this->getInfo()->name));
    }
}
