<?php

namespace App\ValueObjects\Pagination;

use App\Support\Pagination\Paginator;

final readonly class PerPageValue
{
    public int $value;
    
    private function __construct(?string $perPage)
    {
        $intPerPage = intval(trim($perPage ?? ''));
        
        if (!in_array($intPerPage, Paginator::PAGINATOR_PER_PAGE_LIST)) {
            $this->value = Paginator::PAGINATOR_DEFAULT_PER_PAGE;
            return ;
        }
        
        $this->value = $intPerPage;
    }
    
    public static function create(?string $perPage): self
    {
        return new self($perPage);
    }
}
