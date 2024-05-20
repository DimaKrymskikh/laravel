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
        return $request->input('page', self::DEFAULT_CURRENT_PAGE);
    }

    /**
     * Возвращает число элементов на странице
     * 
     * @param Request $request
     * @return int
     */
    public function getNumber(Request $request): int
    {
        return $request->input('number', self::DEFAULT_PER_PAGE);
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
    
    /**
     * Возвращает номер страницы пагинации после удаления элемента.
     * (Либо страницу, на которой был элемент, либо последнюю страницу, если удаляемый элемент был последним на последней странице)
     * 
     * @param Request $request
     * @param int $maxSerialNumber - число элементов в списке
     * @return int
     */
    public function getCurrentPageAfterRemovingItems(Request $request, int $maxSerialNumber): int
    {
        $page = $this->getPage($request);
        $number = $this->getNumber($request);
        
        while($page > $this->getPageOfItem($maxSerialNumber, $number)) {
            $page--;
        }
        
        return $page;
    }
    
    /**
     * Возвращает массив параметров для url, которые используются в фильтре запроса к базе
     * 
     * @param Request $request
     * @return array
     */
    private function setArrayOfAdditionalParamsForUrl(Request $request): array
    {
        $arr = [];
        foreach ($this->additionalParamsInUrl as $value) {
            $arr[$value] = $request->$value;
        }
        
        return $arr;
    }
    
    /**
     * Возвращает массив параметров для url
     * 
     * @param Request $request
     * @param int|null $page - если задан, то это номер текущей страницы пагинации
     * @return array
     */
    public function setParamsArrayForUrl(Request $request, ?int $page = null): array
    {
        return array_merge([
                    'page' => $page ?? $this->getPage($request),
                    'number' => $this->getNumber($request),
                ], $this->setArrayOfAdditionalParamsForUrl($request));

    }
    
    /**
     * Возвращает url со всеми параметрами пагинации
     * 
     * @param string $baseUrl
     * @param Request $request
     * @param int|null $page - если задан, то это номер текущей страницы пагинации
     * @return string
     */
    public function getUrl(string $baseUrl, Request $request, ?int $page = null): string
    {
        return "$baseUrl?".http_build_query($this->setParamsArrayForUrl($request, $page));
    }
}
