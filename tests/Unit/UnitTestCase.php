<?php

namespace Tests\Unit;

use App\Pagination\PaginatorDTO;
use App\Pagination\ValueObjects\PageValue;
use App\Pagination\ValueObjects\PerPageValue;
use PHPUnit\Framework\TestCase;

abstract class UnitTestCase extends TestCase
{
    protected function getPaginatorDTO(): PaginatorDTO
    {
        return new PaginatorDTO(PageValue::create('12'), PerPageValue::create('20'));
    }
}
