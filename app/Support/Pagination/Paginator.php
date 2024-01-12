<?php

namespace App\Support\Pagination;

use App\Support\Pagination\RequestGuard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait Paginator
{
    /**
     * Задаёт пагинацию в запросе $query
     * 
     * @param Builder $query
     * @param Request $request
     * @param RequestGuard $guard
     * @return LengthAwarePaginator
     */
    protected function setPagination(Builder $query, Request $request, RequestGuard $guard): LengthAwarePaginator
    {
        $page = $guard->getPageOfItemByRequest($request);
        $number = $guard->getNumber($request);
        
        return $query->paginate($number)->appends($guard->setParamsArrayForUrl($request, $page, $number));
    }
}
