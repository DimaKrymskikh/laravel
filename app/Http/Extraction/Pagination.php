<?php

namespace App\Http\Extraction;

use Illuminate\Http\Request;

trait Pagination
{
    private const DEFAULT_CURRENT_PAGE = 1;
    private const DEFAULT_PER_PAGE = 20;
    
    /**
     * Возвращает число страниц по общему числу элементов и числу элементов на странице
     * 
     * @param int $total - общее число элементов
     * @param int $perPage - число элементов на странице
     * @return int
     */
    private function getTotalNumberOfPages(int $total, int $perPage): int
    {
        $one = ($total % $perPage) ? 1 : 0;
        
        return $total / $perPage + $one;
    }

    /**
     * Возвращает число элементов на странице
     * 
     * @param Request $request
     * @return int
     */
    private function getNumberPerPage(Request $request): int
    {
        return $request->number ?? self::DEFAULT_PER_PAGE;
    }
    
    /**
     * Возвращает текущий номер страницы пагинации, на которой находится элемент.
     * Если порядковый номер элемента не задан, текущий номер страницы пагинации остаётся неизменным
     * 
     * @param Request $request
     * @param int|null $serialNumber - порядковый номер элемента в списке/таблице
     * @return int
     */
    private function getCurrentPageBySerialNumber(Request $request, ?int $serialNumber = null): int
    {
        $requestPage = $request->page ?? self::DEFAULT_CURRENT_PAGE;
        $requestNumber = $request->number ?? self::DEFAULT_PER_PAGE;
        
        return $serialNumber ? $this->getTotalNumberOfPages($serialNumber, $requestNumber) : $requestPage;
    }
    
    private function getCurrentPageAfterRemovingItems(Request $request, int $totalNumberOfItems): int
    {
        $requestPage = $request->page ?? self::DEFAULT_CURRENT_PAGE;
        $requestNumber = $request->number ?? self::DEFAULT_PER_PAGE;
        
        while($requestPage > $this->getTotalNumberOfPages($totalNumberOfItems, $requestNumber)) {
            $requestPage--;
        }
        
        return $requestPage;
    }
}
