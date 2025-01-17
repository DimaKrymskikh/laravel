<?php

namespace App\ValueObjects\Pagination;

use App\Support\Pagination\Paginator;

final readonly class PageValue
{
    public int $value;
    
    private function __construct(?string $page)
    {
        $intPage = intval(trim($page ?? ''));
        
        // Номер страницы должен быть положительным
        if ($intPage <= 0 || $intPage === PHP_INT_MAX) {
            $this->value = Paginator::PAGINATOR_DEFAULT_CURRENT_PAGE;
            return ;
        }
        
        $this->value = $intPage;
    }
    
    public static function create(?string $page): self
    {
        return new self($page);
    }
}
