<?php

namespace App\Services\Quiz\Enums\QuizStatuses;

use App\Services\Quiz\Enums\QuizStatus;
use Illuminate\Support\Collection;

final class AtWorkStatus extends InfoQuizStatus
{
    public function __construct()
    {
        $this->name = 'в работе';
        $this->style = 'status-sky';
        $this->isEditable = true;
        $this->colorSvg = 'red';
        $this->titleSvg = "Опрос имеет статус 'в работе', нельзя перевести в статус 'утверждён'";
    }
    
    /**
     * {@inheritDoc}
     */
    public function getNextStatuses(): Collection
    {
        return collect([QuizStatus::Removed]);
    }
}
