<?php

namespace App\Services\Quiz\Enums\QuizStatuses;

use App\Services\Quiz\Enums\QuizStatus;
use Illuminate\Support\Collection;

final class ReadyStatus extends InfoQuizStatus
{
    public function __construct()
    {
        $this->name = 'готов';
        $this->style = 'status-yellow';
        $this->isEditable = true;
        $this->colorSvg = 'yellow';
        $this->titleSvg = "Опрос имеет статус 'готов', хотите перевести опрос в статус 'утверждён'?";
    }
    
    /**
     * {@inheritDoc}
     */
    public function getNextStatuses(): Collection
    {
        return collect([QuizStatus::Approved, QuizStatus::Removed]);
    }
}
