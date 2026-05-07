<?php

namespace App\Services\Quiz\Enums\QuizItemStatuses;

use App\Services\Quiz\Enums\QuizItemStatus;
use Illuminate\Support\Collection;

final class ReadyStatus extends InfoQuizItemStatus
{
    public function __construct()
    {
        $this->name = 'готов';
        $this->style = 'status-green';
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
