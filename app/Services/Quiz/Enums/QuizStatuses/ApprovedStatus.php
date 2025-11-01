<?php

namespace App\Services\Quiz\Enums\QuizStatuses;

use App\Services\Quiz\StatusInterface;
use Illuminate\Support\Collection;

final readonly class ApprovedStatus implements StatusInterface
{
    public string $name;
    public string $style;
    public bool $isEditable; // Можно ли редактировать опрос в данном статусе
    public string $colorSvg;
    public string $titleSvg;
    
    public function __construct()
    {
        $this->name = 'утверждён';
        $this->style = 'status-green';
        $this->isEditable = false;
        $this->colorSvg = 'green';
        $this->titleSvg = "Опрос имеет статус 'утверждён', хотите отменить этот статус?";
    }
    
    public function getNextStatuses(): Collection
    {
        return collect([]);
    }
}
