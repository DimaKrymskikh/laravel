<?php

namespace Tests\Unit\Http\Extraction;

use App\Http\Extraction\Pagination;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    use Pagination;
    
    public function test_method_getTotalNumberOfPages(): void
    {
        // Деление без остатка
        $this->assertEquals(3, $this->getTotalNumberOfPages(15, 5));
        // Деление с остатком
        $this->assertEquals(4, $this->getTotalNumberOfPages(16, 5));
    }
    
    public function test_method_getNumberPerPage(): void
    {
        $this->assertEquals(10, $this->getNumberPerPage(new Request(['number' => 10])));
        $this->assertEquals(self::DEFAULT_PER_PAGE, $this->getNumberPerPage(new Request));
    }
    
    public function test_method_getPageNumber(): void
    {
        $request = new Request([
            'number' => 10,
            'page' => 2
        ]);
        $this->assertEquals(2, $this->getCurrentPageBySerialNumber($request));
        // Деление с остатком
        $this->assertEquals(13, $this->getCurrentPageBySerialNumber($request, 125  /** 12 * 10 + 5 **/));
        // Деление без остатка
        $this->assertEquals(12, $this->getCurrentPageBySerialNumber($request, 120 /** 12 * 10 **/));
        
        $defaultRequest = new Request();
        $this->assertEquals(self::DEFAULT_CURRENT_PAGE, $this->getCurrentPageBySerialNumber($defaultRequest));
        $this->assertEquals(6, $this->getCurrentPageBySerialNumber($defaultRequest, 105));
    }
    
    public function test_method_getCurrentNumberAfterRemovingItems(): void
    {
        $request = new Request([
            'number' => 10,
            'page' => 2
        ]);
        // Элементы размещаются на одной странице, текущая страница пагинации уменьшается
        $this->assertEquals(1, $this->getCurrentPageAfterRemovingItems($request, 10));
        // Элементы размещаются на двух страницах, текущая страница пагинации не изменяется
        $this->assertEquals(2, $this->getCurrentPageAfterRemovingItems($request, 11));
    }
}
