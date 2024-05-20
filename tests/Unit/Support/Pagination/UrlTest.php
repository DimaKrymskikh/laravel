<?php

namespace Tests\Unit\Support\Pagination;

use App\Contracts\Repositories\ListItem;
use App\Support\Pagination\Url;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    private int $itemId = 12;
    private int $serialNumber = 25;
    private int $itemsSum = 55;
    
    private ?Request $request;
    private ?Url $url;

    public function test_method_getUrlByItem(): void
    {
        $mock = Mockery::mock(ListItem::class);
        $mock->shouldReceive('getSerialNumberOfItemInList')
                ->once()
                ->with($this->request, $this->itemId)
                ->andReturn($this->serialNumber);
        
        // page=3, потому что 25 находится на 3-й странице при 10 элементах на странице
        $this->assertEquals('base?page=3&number=10&filter=abc', $this->url->getUrlByItem('base', $this->request, $mock, $this->itemId));
    }
    
    public function test_getUrlByRequest(): void
    {
        $this->assertEquals('base?page=7&number=10&filter=abc', $this->url->getUrlByRequest('base', $this->request));
    }
    
    public function test_getUrlAfterRemovingItem(): void
    {
        $mock = Mockery::mock(ListItem::class);
        $mock->shouldReceive('getNumberOfItemsInList')
                ->once()
                ->with($this->request)
                ->andReturn($this->itemsSum);
        
        // 55 = 5 * 10 + 5, поэтому page=6
        $this->assertEquals('base?page=6&number=10&filter=abc', $this->url->getUrlAfterRemovingItem('base', $this->request, $mock));
    }
    
    protected function setUp(): void
    {
        $this->request = new Request(['page' => 7, 'number' => 10, 'filter' => 'abc']);
        $this->url = new Url(['filter']);
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        $this->request = null;
        $this->url = null;
    }
}
