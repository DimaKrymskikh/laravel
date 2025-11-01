<?php

namespace App\Services\Quiz\Enums\QuizStatuses;

use App\Services\Quiz\StatusInterface;
use Illuminate\Support\Collection;

final readonly class RemovedStatus implements StatusInterface
{
    public string $name;
    public string $style;
    public bool $isEditable; // Можно ли редактировать опрос в данном статусе
    public string $colorSvg;
    public string $titleSvg;
    
    public function __construct()
    {
        $this->name = 'удалён';
        $this->style = 'status-gray';
        $this->isEditable = false;
        $this->colorSvg = 'gray';
        $this->titleSvg = "Опрос имеет статус 'удалён', нельзя перевести в статус 'утверждён'";
    }
    
    public function getNextStatuses(): Collection
    {
        return collect([]);
    }
}
