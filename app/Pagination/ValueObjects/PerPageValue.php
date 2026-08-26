<?php

namespace App\Pagination\ValueObjects;

use App\Pagination\PaginatorDTO;

/**
 * Класс хранит валидное число элементов на странице пагинации.
 */
final readonly class PerPageValue
{
    public int $value;
    
    private function __construct(?string $perPage)
    {
        $intPerPage = intval(trim($perPage ?? ''));
        
        if (!in_array($intPerPage, PaginatorDTO::PAGINATOR_PER_PAGE_LIST)) {
            $this->value = PaginatorDTO::PAGINATOR_DEFAULT_PER_PAGE;
            return ;
        }
        
        $this->value = $intPerPage;
    }
    
    public static function create(?string $perPage): self
    {
        return new self($perPage);
    }
}
