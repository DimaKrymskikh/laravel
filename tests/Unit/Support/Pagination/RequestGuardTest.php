<?php

namespace Tests\Unit\Support\Pagination;

use App\Support\Pagination\RequestGuard;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestGuardTest extends TestCase
{
    public function test_method_getPage(): void
    {
        $guard = new RequestGuard([]);
        
        $this->assertEquals(7, $guard->getPage(new Request(['page' => 7])));
        $this->assertEquals(RequestGuard::DEFAULT_CURRENT_PAGE, $guard->getPage(new Request));
    }
    
    public function test_method_getNumber(): void
    {
        $guard = new RequestGuard([]);
        
        $this->assertEquals(10, $guard->getNumber(new Request(['number' => 10])));
        $this->assertEquals(RequestGuard::DEFAULT_PER_PAGE, $guard->getNumber(new Request));
    }
    
    public function test_method_getPageOfItemByRequest(): void
    {
        $guard = new RequestGuard([]);
        
        $request = new Request([
            'number' => 10,
            'page' => 2
        ]);
        $this->assertEquals(2, $guard->getPageOfItemByRequest($request));
        // Деление с остатком
        $this->assertEquals(13, $guard->getPageOfItemByRequest($request, 125  /** 12 * 10 + 5 **/));
        // Деление без остатка
        $this->assertEquals(12, $guard->getPageOfItemByRequest($request, 120 /** 12 * 10 **/));
        
        $defaultRequest = new Request();
        $this->assertEquals(RequestGuard::DEFAULT_CURRENT_PAGE, $guard->getPageOfItemByRequest($defaultRequest));
        $this->assertEquals(6, $guard->getPageOfItemByRequest($defaultRequest, 103 /** 5 * 20 + 3 **/));
    }
    
    public function test_method_getCurrentPageAfterRemovingItems(): void
    {
        $guard = new RequestGuard([]);
        
        $request = new Request([
            'number' => 10,
            'page' => 2
        ]);
        // Элементы размещаются на одной странице, текущая страница пагинации уменьшается
        $this->assertEquals(1, $guard->getCurrentPageAfterRemovingItems($request, 10));
        // Элементы размещаются на двух страницах, текущая страница пагинации не изменяется
        $this->assertEquals(2, $guard->getCurrentPageAfterRemovingItems($request, 11));
    }
}
