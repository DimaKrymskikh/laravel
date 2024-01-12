<?php

namespace App\Support\Pagination;

use Illuminate\Http\Request;

class RequestGuard
{
    public const DEFAULT_CURRENT_PAGE = 1;
    public const DEFAULT_PER_PAGE = 20;
    public const DEFAULT_SERIAL_NUMBER = 1;
    
    public function __construct(
            private array $additionalParamsInUrl,
    )
    {}
    
    /**
     * Возвращает текущую страницу пагинации
     * 
     * @param Request $request
     * @return int
     */
    public function getPage(Request $request): int
    {
        return $request->page ?? self::DEFAULT_CURRENT_PAGE;
    }

    /**
     * Возвращает число элементов на странице
     * 
     * @param Request $request
     * @return int
     */
    public function getNumber(Request $request): int
    {
        return $request->number ?? self::DEFAULT_PER_PAGE;
    }

    /**
     * Возвращает номер страницы, на которой находится элемент из списка
     * 
     * @param int $serialNumber - порядковый номер элемента в списке
     * @param int $number - число элементов на странице
     * @return int
     */
    private function getPageOfItem(int $serialNumber, int $number): int
    {
        $one = ($serialNumber % $number) ? 1 : 0;
        
        return $serialNumber / $number + $one;
    }
    
    /**
     * Возвращает номер страницы пагинации, на которой находится элемент.
     * Если порядковый номер элемента не задан, текущий номер страницы пагинации определяется по данным запроса
     * 
     * @param Request $request
     * @param int|null $serialNumber - порядковый номер элемента в списке/таблице
     * @return int
     */
    public function getPageOfItemByRequest(Request $request, ?int $serialNumber = null): int
    {
        $page = $this->getPage($request);
        $number = $this->getNumber($request);
        
        return $serialNumber ? $this->getPageOfItem($serialNumber, $number) : $page;
    }
    
    public function getCurrentPageAfterRemovingItems(Request $request, int $maxSerialNumber): int
    {
        $page = $this->getPage($request);
        $number = $this->getNumber($request);
        
        while($page > $this->getPageOfItem($maxSerialNumber, $number)) {
            $page--;
        }
        
        return $page;
    }
    
    private function setArrayOfAdditionalParamsForUrl(Request $request): array
    {
        $arr = [];
        foreach ($this->additionalParamsInUrl as $value) {
            $arr[$value] = $request->$value;
        }
        
        return $arr;
    }
    
    public function setParamsArrayForUrl(Request $request, int $page, int $number): array
    {
        return array_merge([
                    'page' => $page,
                    'number' => $number,
                ], $this->setArrayOfAdditionalParamsForUrl($request));

    }
    
    public function getUrl(string $baseUrl, Request $request, int $page, int $number): string
    {
        return "$baseUrl?".http_build_query($this->setParamsArrayForUrl($request, $page, $number));
    }
}
