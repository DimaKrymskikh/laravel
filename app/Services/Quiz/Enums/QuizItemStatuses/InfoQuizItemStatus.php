<?php

namespace App\Services\Quiz\Enums\QuizItemStatuses;

use App\Services\Quiz\StatusInterface;
use Illuminate\Support\Collection;

abstract class InfoQuizItemStatus implements StatusInterface
{
    public string $name;
    public string $style;
    public bool $isEditable; // Можно ли редактировать вопрос в данном статусе
    
    /**
     * Для текущего статуса вопроса возвращает коллекцию возможных статусов,
     * в которые может перейти статус вопроса при ручном управлении.
     * 
     * @return Collection
     */
    abstract public function getNextStatuses(): Collection;
}
