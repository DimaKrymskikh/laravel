<?php

namespace App\Http\Extraction\Dvd;

use App\Http\Extraction\Pagination;
use App\Models\Dvd\Actor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

trait Actors
{
    use Pagination;
    
    private function setActorsPagination(Builder $query, Request $request): LengthAwarePaginator
    {
        return $this->setPagination($query, $request, ['name']);
    }

    private function queryCommonActorsList(Request $request): Builder
    {
        return Actor::select(
                'id',
                'first_name',
                'last_name',
                DB::raw('row_number() OVER(ORDER BY first_name, last_name) AS n')
            )
            ->when($request->name, function (Builder $query, string $name) {
                $query->whereRaw("concat(first_name, ' ', last_name) ILIKE ?", ["%$name%"]);
            })
            ->orderBy('first_name')
            ->orderBy('last_name');
    }

    /**
     * Возвращает общий список актёров с сортировкой по имени и фамилии с пагинацией или без
     * 
     * @param Request $request
     * @param bool $isPagination = true - с пагинацией
     * @return LengthAwarePaginator|Actor
     */
    private function getCommonActorsList(Request $request, bool $isPagination = true): LengthAwarePaginator | Collection
    {
        $query =  $this->queryCommonActorsList($request);
                
        return $isPagination ? $this->setActorsPagination($query, $request) : $query->get();
    }
    
    /**
     * Возвращает номер актёра в списке/таблице актёров
     * 
     * @param int $actorId - id актёра
     * @return int
     */
    private function getSerialNumberOfTheActorInTheList(Request $request, int $actorId): int
    {
        $actor = $this->queryCommonActorsList($request)->get()->find($actorId);
        
        return $actor ? $actor->n : self::DEFAULT_SERIAL_NUMBER;
    }
}
