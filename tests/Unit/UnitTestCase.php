<?php

namespace Tests\Unit;

use App\DataTransferObjects\Pagination\PaginatorDto;
use App\ValueObjects\Pagination\PageValue;
use App\ValueObjects\Pagination\PerPageValue;
use PHPUnit\Framework\TestCase;

abstract class UnitTestCase extends TestCase
{
    protected function getPaginatorDto(): PaginatorDto
    {
        return new PaginatorDto(PageValue::create('12'), PerPageValue::create('20'));
    }
}
