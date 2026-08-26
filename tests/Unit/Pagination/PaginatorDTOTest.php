<?php

namespace Tests\Unit\Support\Pagination;

use App\Pagination\ValueObjects\PageValue;
use App\Pagination\ValueObjects\PerPageValue;
use App\Pagination\PaginatorDTO;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PaginatorDTOTest extends TestCase
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
    public function test_getСurrentPageByItemNumber(int $result, int $itemNumber, int $perPage): void
    {
        $dto = new PaginatorDTO(PageValue::create('1'), PerPageValue::create((string) $perPage));
        
        $this->assertEquals($result, $dto->getСurrentPageByItemNumber($itemNumber)->value);
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
        $dto = new PaginatorDTO(PageValue::create((string) $page), PerPageValue::create((string) $perPage));
        
        $this->assertEquals($result, $dto->getСurrentPage($maxItemNumber)->value);
    }
}
