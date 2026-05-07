<?php

namespace App\Services\Quiz\Enums\QuizItemStatuses;

use App\Services\Quiz\Enums\QuizItemStatus;
use Illuminate\Support\Collection;

final class AtWorkStatus extends InfoQuizItemStatus
{
    public function __construct()
    {
        $this->name = 'в работе';
        $this->style = 'status-sky';
        $this->isEditable = true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getNextStatuses(): Collection
    {
        return collect([QuizItemStatus::Removed]);
    }
}
