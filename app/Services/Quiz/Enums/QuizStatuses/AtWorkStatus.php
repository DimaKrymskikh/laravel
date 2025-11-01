<?php

namespace App\Services\Quiz\Enums\QuizStatuses;

use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\StatusInterface;
use Illuminate\Support\Collection;

final readonly class AtWorkStatus implements StatusInterface
{
    public string $name;
    public string $style;
    public bool $isEditable; // Можно ли редактировать опрос в данном статусе
    public string $colorSvg;
    public string $titleSvg;
    
    public function __construct()
    {
        $this->name = 'в работе';
        $this->style = 'status-sky';
        $this->isEditable = true;
        $this->colorSvg = 'red';
        $this->titleSvg = "Опрос имеет статус 'в работе', нельзя перевести в статус 'утверждён'";
    }
    
    public function getNextStatuses(): Collection
    {
        return collect([QuizStatus::Removed]);
    }
}
