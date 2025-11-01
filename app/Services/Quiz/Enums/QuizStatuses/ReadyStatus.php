<?php

namespace App\Services\Quiz\Enums\QuizStatuses;

use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\StatusInterface;
use Illuminate\Support\Collection;

final readonly class ReadyStatus implements StatusInterface
{
    public string $name;
    public string $style;
    public bool $isEditable; // Можно ли редактировать опрос в данном статусе
    public string $colorSvg;
    public string $titleSvg;
    
    public function __construct()
    {
        $this->name = 'готов';
        $this->style = 'status-yellow';
        $this->isEditable = true;
        $this->colorSvg = 'yellow';
        $this->titleSvg = "Опрос имеет статус 'готов', хотите перевести опрос в статус 'утверждён'?";
    }
    
    public function getNextStatuses(): Collection
    {
        return collect([QuizStatus::Approved, QuizStatus::Removed]);
    }
}
