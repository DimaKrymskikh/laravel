<?php

namespace Tests\Unit\Support\Pagination;

use App\Support\Pagination\Paginator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    public static function getPageOfItemProvider(): array
    {
        // $result, $itemNumber, $perPage
        return [
            [2, 12, 10],
            [2, 20, 10],
            [3, 25, 10],
        ];
    }
    
    #[DataProvider('getPageOfItemProvider')]
    public function test_get_page_of_item(int $result, int $itemNumber, int $perPage): void
    {
        $paginator = new Paginator();
        
        $this->assertSame($result, $paginator->getPageOfItem($itemNumber, $perPage));
    }
    
    public static function getCurrentPageProvider(): array
    {
        // $result, $maxItemNumber, $page, $perPage
        return [
            [2, 20, 2, 10],
            [3, 25, 3, 10],
            // $result < $page
            [2, 20, 3, 10],
        ];
    }
    
    #[DataProvider('getCurrentPageProvider')]
    public function test_get_current_page(int $result, int $maxItemNumber, int $page, int $perPage): void
    {
        $paginator = new Paginator();
        
        $this->assertSame($result, $paginator->getCurrentPage($maxItemNumber, $page, $perPage));
    }
}
