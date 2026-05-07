<?php

namespace App\Services\Quiz\Enums\QuizItemStatuses;

use Illuminate\Support\Collection;

final class RemovedStatus extends InfoQuizItemStatus
{
    public function __construct()
    {
        $this->name = 'удалён';
        $this->style = 'status-gray';
        $this->isEditable = false;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getNextStatuses(): Collection
    {
        return collect([]);
    }
}
