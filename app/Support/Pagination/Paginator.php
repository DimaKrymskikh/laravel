<?php

namespace App\Support\Pagination;

final class Paginator
{
    public const PAGINATOR_DEFAULT_PER_PAGE = 20;
    public const PAGINATOR_DEFAULT_ITEM_NUMBER = 1;
    public const PAGINATOR_DEFAULT_CURRENT_PAGE = 1;
    public const PAGINATOR_PER_PAGE_LIST = [10, 20, 50, 100, 1000];
    
    /**
     * Определяет на какой странице находиться элемент
     * 
     * @param int $itemNumber - порядковый номер элемента
     * @param int $perPage - число элементов на странице
     * @return int
     */
    public function getPageOfItem(int $itemNumber, int $perPage): int
    {
        return (int) ceil($itemNumber / $perPage);
    }
    
    /**
     * Определяет текущую страницу пагинации.
     * (Метод полезен после удаления элементов)
     * 
     * @param int $maxItemNumber - наибольший порядковый номер в таблице
     * @param int $page - текущая страница пагинации на момент вызова метода
     * @param int $perPage - число элементов на странице
     * @return int
     */
    public function getCurrentPage(int $maxItemNumber, int $page, int $perPage): int
    {
        $maxPage = $this->getPageOfItem($maxItemNumber, $perPage);
        
        return min($maxPage, $page);
    }
}
