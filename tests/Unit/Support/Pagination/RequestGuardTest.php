<?php

namespace Tests\Unit\Support\Pagination;

use App\Support\Pagination\RequestGuard;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestGuardTest extends TestCase
{
    private ?RequestGuard $emptyGuard;
    private ?RequestGuard $guardWithTwoParams;
    
    private ?Request $emptyRequest;
    private ?Request $halfRequest;
    private ?Request $fullRequest;

    public function test_method_getPage(): void
    {
        $this->assertEquals(7, $this->emptyGuard->getPage(new Request(['page' => 7])));
        $this->assertEquals(RequestGuard::DEFAULT_CURRENT_PAGE, $this->emptyGuard->getPage($this->emptyRequest));
    }
    
    public function test_method_getNumber(): void
    {
        $this->assertEquals(10, $this->emptyGuard->getNumber(new Request(['number' => 10])));
        $this->assertEquals(RequestGuard::DEFAULT_PER_PAGE, $this->emptyGuard->getNumber($this->emptyRequest));
    }
    
    public function test_method_getPageOfItemByRequest(): void
    {
        $this->assertEquals(2, $this->emptyGuard->getPageOfItemByRequest($this->halfRequest));
        // Деление с остатком
        $this->assertEquals(13, $this->emptyGuard->getPageOfItemByRequest($this->halfRequest, 125  /** 12 * 10 + 5 **/));
        // Деление без остатка
        $this->assertEquals(12, $this->emptyGuard->getPageOfItemByRequest($this->halfRequest, 120 /** 12 * 10 **/));
        
        $this->assertEquals(RequestGuard::DEFAULT_CURRENT_PAGE, $this->emptyGuard->getPageOfItemByRequest($this->emptyRequest));
        $this->assertEquals(6, $this->emptyGuard->getPageOfItemByRequest($this->emptyRequest, 103 /** 5 * 20 + 3 **/));
    }
    
    public function test_method_getCurrentPageAfterRemovingItems(): void
    {
        // Элементы размещаются на одной странице, текущая страница пагинации уменьшается
        $this->assertEquals(1, $this->emptyGuard->getCurrentPageAfterRemovingItems($this->halfRequest, 10));
        // Элементы размещаются на двух страницах, текущая страница пагинации не изменяется
        $this->assertEquals(2, $this->emptyGuard->getCurrentPageAfterRemovingItems($this->halfRequest, 11));
    }
    
    public function test_method_setParamsArrayForUrl(): void
    {
        $this->assertEqualsCanonicalizing([
                'page' => 2,
                'number' => 10,
                'param1' => '123',
                'param2' => 'ab'
            ], $this->guardWithTwoParams->setParamsArrayForUrl($this->fullRequest));
        $this->assertEqualsCanonicalizing([
                'page' => 7,
                'number' => 10,
                'param1' => '123',
                'param2' => 'ab'
            ], $this->guardWithTwoParams->setParamsArrayForUrl($this->fullRequest, 7));
        
        $this->assertEqualsCanonicalizing([
                'page' => 2,
                'number' => 10,
            ], $this->emptyGuard->setParamsArrayForUrl($this->fullRequest));
        $this->assertEqualsCanonicalizing([
                'page' => 7,
                'number' => 10,
            ], $this->emptyGuard->setParamsArrayForUrl($this->fullRequest, 7));
        
        $this->assertEqualsCanonicalizing([
                'page' => RequestGuard::DEFAULT_CURRENT_PAGE,
                'number' => RequestGuard::DEFAULT_PER_PAGE,
            ], $this->emptyGuard->setParamsArrayForUrl($this->emptyRequest));
        $this->assertEqualsCanonicalizing([
                'page' => 7,
                'number' => RequestGuard::DEFAULT_PER_PAGE,
            ], $this->emptyGuard->setParamsArrayForUrl($this->emptyRequest, 7));
    }
    
    public function test_getUrl(): void
    {
        $this->assertEquals('base?page='.RequestGuard::DEFAULT_CURRENT_PAGE.'&number='.RequestGuard::DEFAULT_PER_PAGE, $this->guardWithTwoParams->getUrl('base', $this->emptyRequest));
        $this->assertEquals('base?page=7&number='.RequestGuard::DEFAULT_PER_PAGE, $this->guardWithTwoParams->getUrl('base', $this->emptyRequest, 7));
        
        $this->assertEquals('base?page=2&number=10', $this->guardWithTwoParams->getUrl('base', $this->halfRequest));
        $this->assertEquals('base?page=7&number=10', $this->guardWithTwoParams->getUrl('base', $this->halfRequest, 7));
        
        $this->assertEquals('base?page=2&number=10&param1=123&param2=ab', $this->guardWithTwoParams->getUrl('base', $this->fullRequest));
        $this->assertEquals('base?page=7&number=10&param1=123&param2=ab', $this->guardWithTwoParams->getUrl('base', $this->fullRequest, 7));
}
    
    protected function setUp(): void
    {
        $this->emptyGuard = new RequestGuard([]);
        $this->guardWithTwoParams = new RequestGuard(['param1', 'param2']);
        
        $this->emptyRequest = new Request();
        $this->halfRequest = new Request([
            'number' => 10,
            'page' => 2
        ]);
        $this->fullRequest = new Request([
            'number' => 10,
            'page' => 2,
            'param1' => '123',
            'param2' => 'ab',
            'data' => 'xyz'
        ]);
    }

    protected function tearDown(): void
    {
        $this->emptyGuard = null;
        $this->guardWithTwoParams = null;
        
        $this->emptyRequest = null;
        $this->halfRequest = null;
        $this->fullRequest = null;
    }
}
