<?php

namespace App\Services\Quiz\Enums\QuizItemStatuses;

use App\Services\Quiz\StatusInterface;
use Illuminate\Support\Collection;

final readonly class RemovedStatus implements StatusInterface
{
    public string $name;
    public string $style;
    public bool $isEditable; // Можно ли редактировать вопрос в данном статусе
    
    public function __construct()
    {
        $this->name = 'удалён';
        $this->style = 'status-gray';
        $this->isEditable = false;
    }
    
    public function getNextStatuses(): Collection
    {
        return collect([]);
    }
}
