<?php

namespace App\Services\Quiz\Enums\QuizStatuses;

use Illuminate\Support\Collection;

final class ApprovedStatus extends InfoQuizStatus
{
    public function __construct()
    {
        $this->name = 'утверждён';
        $this->style = 'status-green';
        $this->isEditable = false;
        $this->colorSvg = 'green';
        $this->titleSvg = "Опрос имеет статус 'утверждён', хотите отменить этот статус?";
    }
    
    /**
     * {@inheritDoc}
     */
    public function getNextStatuses(): Collection
    {
        return collect([]);
    }
}
