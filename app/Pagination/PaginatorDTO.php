<?php

namespace App\Pagination;

use App\Pagination\ValueObjects\PageValue;
use App\Pagination\ValueObjects\PerPageValue;

/**
 * Класс содержит валидные параметры пагинации: номер страницы и число элементов на странице.
 */
final readonly class PaginatorDTO
{
    public const PAGINATOR_DEFAULT_PER_PAGE = 20;
    public const PAGINATOR_DEFAULT_ITEM_NUMBER = 1;
    public const PAGINATOR_DEFAULT_CURRENT_PAGE = 1;
    public const PAGINATOR_PER_PAGE_LIST = [10, 20, 50, 100, 1000];
    
    public function __construct(
            public PageValue $page,
            public PerPageValue $perPage
    ) {
    }
    
    /**
     * Возвращает страницу, на которой находится элемент.
     * 
     * @param int $itemNumber Номер элемента в списке.
     * @return PageValue
     */
    public function getСurrentPageByItemNumber(int $itemNumber): PageValue
    {
        return PageValue::create(ceil($itemNumber / $this->perPage->value));
    }
    
    /**
     * Возвращает текущую страницу пагинации с учётом возможного удаления элементов.
     * 
     * @param int $total Количество элементов.
     * @return PageValue
     */
    public function getСurrentPage(int $total): PageValue
    {
        $maxPage = $this->getСurrentPageByItemNumber($total)->value;
        
        return PageValue::create(min($maxPage, $this->page->value));
    }
}
