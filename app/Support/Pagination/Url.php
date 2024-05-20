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
    
    /**
     * Возвращает такой url, чтобы нужный элемент находился на текущей странице пагинации
     * 
     * @param string $baseUrl
     * @param Request $request
     * @param ListItem $items
     * @param int $itemId
     * @return string
     */
    public function getUrlByItem(string $baseUrl, Request $request, ListItem $items, int $itemId): string
    {
        $page = $this->quard->getPageOfItemByRequest($request, $items->getSerialNumberOfItemInList($request, $itemId));
        
        return $this->quard->getUrl($baseUrl, $request, $page);
    }
    
    /**
     * Возвращает url по данным запроса
     * 
     * @param string $baseUrl
     * @param Request $request
     * @return string
     */
    public function getUrlByRequest(string $baseUrl, Request $request): string
    {
        return $this->quard->getUrl($baseUrl, $request);
    }
    
    /**
     * Возвращает такой url, чтобы текущей страницей пагинации была страница, на которой был удалённый элемент,
     * либо последней страницей, если удалённый элемент был последним на последней странице
     * 
     * @param string $baseUrl
     * @param Request $request
     * @param ListItem $items
     * @return string
     */
    public function getUrlAfterRemovingItem(string $baseUrl, Request $request, ListItem $items): string
    {
        $page = $this->quard->getCurrentPageAfterRemovingItems($request, $items->getNumberOfItemsInList($request));
        
        return $this->quard->getUrl($baseUrl, $request, $page);
    }
}
