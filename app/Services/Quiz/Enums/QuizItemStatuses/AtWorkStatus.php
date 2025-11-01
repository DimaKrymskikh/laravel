<?php

namespace App\Services\Quiz\Enums\QuizItemStatuses;

use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\StatusInterface;
use Illuminate\Support\Collection;

final readonly class AtWorkStatus implements StatusInterface
{
    public string $name;
    public string $style;
    public bool $isEditable; // Можно ли редактировать вопрос в данном статусе
    
    public function __construct()
    {
        $this->name = 'в работе';
        $this->style = 'status-sky';
        $this->isEditable = true;
    }
    
    public function getNextStatuses(): Collection
    {
        return collect([QuizItemStatus::Removed]);
    }
}
