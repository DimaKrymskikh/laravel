<?php

namespace App\Services\Quiz\Enums\QuizStatuses;

use Illuminate\Support\Collection;

final class RemovedStatus extends InfoQuizStatus
{
    public function __construct()
    {
        $this->name = 'удалён';
        $this->style = 'status-gray';
        $this->isEditable = false;
        $this->colorSvg = 'gray';
        $this->titleSvg = "Опрос имеет статус 'удалён', нельзя перевести в статус 'утверждён'";
    }
    
    /**
     * {@inheritDoc}
     */
    public function getNextStatuses(): Collection
    {
        return collect([]);
    }
}
