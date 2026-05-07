<?php

namespace App\Services\Quiz\Enums\QuizStatuses;

use App\Services\Quiz\StatusInterface;
use Illuminate\Support\Collection;

abstract class InfoQuizStatus implements StatusInterface
{
    public string $name;
    public string $style;
    public bool $isEditable; // Можно ли редактировать опрос в данном статусе
    public string $colorSvg;
    public string $titleSvg;
    
    /**
     * Для текущего статуса опроса возвращает коллекцию возможных статусов,
     * в которые может перейти статус опроса при ручном управлении.
     * 
     * @return Collection
     */
    abstract public function getNextStatuses(): Collection;
}
