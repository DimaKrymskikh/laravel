<?php

namespace Tests\Support\Data\Dto\Pagination;

use App\DataTransferObjects\Pagination\PaginatorDto;
use App\ValueObjects\Pagination\PageValue;
use App\ValueObjects\Pagination\PerPageValue;

trait PaginatorDtoCase
{
    private function getBaseCasePaginatorDto(): PaginatorDto
    {
        return new PaginatorDto(PageValue::create('12'), PerPageValue::create('20'));
    }
}
