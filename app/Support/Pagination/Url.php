<?php

namespace App\Support\Pagination;

use App\Contracts\Repositories\ListItem;
use App\Support\Pagination\RequestGuard;
use Illuminate\Http\Request;

class Url
{
    private RequestGuard $quard;

    public function __construct(array $additionalParamsInUrl)
    {
        $this->quard = new RequestGuard($additionalParamsInUrl);
    }
    
    public function getUrlByItem(string $baseUrl, Request $request, ListItem $items, int $filmId): string
    {
        $page = $this->quard->getPageOfItemByRequest($request, $items->getSerialNumberOfItemInList($request, $filmId));
        $number = $this->quard->getNumber($request);
        
        return $this->quard->getUrl($baseUrl, $request, $page, $number);
    }
    
    public function getUrlByRequest(string $baseUrl, Request $request): string
    {
        $page = $this->quard->getPage($request);
        $number = $this->quard->getNumber($request);
        
        return $this->quard->getUrl($baseUrl, $request, $page, $number);
    }
    
    public function getUrlAfterRemovingItem(string $baseUrl, Request $request, ListItem $items): string
    {
        $page = $this->quard->getCurrentPageAfterRemovingItems($request, $items->getNumberOfItemsInList($request));
        $number = $this->quard->getNumber($request);
        
        return $this->quard->getUrl($baseUrl, $request, $page, $number);
    }
}
