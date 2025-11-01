<?php

namespace App\Services\Quiz\Enums;

use App\Services\Quiz\StatusInterface;

/**
 * Статусы опроса
 */
enum QuizStatus: string
{
    // Значения соответствуют табличному типу quiz.quiz_status
    case AtWork = 'at_work';
    case Ready = 'ready';
    case Removed = 'removed';
    case Approved = 'approved';
    
    public const AUTOMATIC_STATUSES = [self::AtWork, self::Ready];
    public const MANUAL_CONTROL_STATUSES = [self::Removed, self::Approved];
    public const MESSAGE_NOT_STATUS_VALUE = 'Строка "%s" не может быть значением статуса опроса.';
    public const MESSAGE_FINAL_STATUS_NOT_EDITABLE = 'Статус опроса "%s" является финальным, поэтому опрос и все его составляющие нельзя редактировать.';
    public const MESSAGE_NOT_FINAL_STATUS = 'Статус опроса "%s" не является финальным.';
    public const MESSAGE_STATUS_NOT_FOR_USERS = 'Опрос со статусом "%s" не может быть представлен пользователям.';

    public function getInfo(): StatusInterface
    {
        $status = '\App\Services\Quiz\Enums\QuizStatuses\\'.$this->name.'Status';
        
        return new $status();
    }
}
