<?php

namespace App\DataTransferObjects\Pagination;

use App\ValueObjects\Pagination\PageValue;
use App\ValueObjects\Pagination\PerPageValue;

final readonly class PaginatorDto
{
    public function __construct(
        public PageValue $page,
        public PerPageValue $perPage
    ) {
    }
}
