<?php

namespace App\Http\Extraction\Dvd;

use App\Http\Extraction\Pagination;
use App\Models\Dvd\Actor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

trait Actors
{
    use Pagination;
    
    /**
     * Возвращает общий список актёров с сортировкой по имени и фамилии с пагинацией
     * 
     * @param Request $request
     * @return LengthAwarePaginator
     */
    private function getCommonActorsList(Request $request): LengthAwarePaginator
    {
        $query =  Actor::select('id', 'first_name', 'last_name')
                ->when($request->name, function (Builder $query, string $name) {
                    $query->where(function (QueryBuilder $subquery) {
                        $subquery->selectRaw("concat(first_name, ' ', last_name)")
                            ->from('dvd.actors as a')
                            ->whereColumn('a.id', 'dvd.actors.id');
                    }, 'ILIKE', "%$name%");
                })
                ->orderBy('first_name')
                ->orderBy('last_name');
                
        return $this->setPagination($query, $request);
    }

    /**
     * Задаёт пагинацию в запросе $query
     * 
     * @param Builder $query
     * @param Request $request
     * @param int|null $serialNumber - номер актёра в списке/таблице актёров
     * @return LengthAwarePaginator
     */
    private function setPagination(Builder $query, Request $request, ?int $serialNumber = null): LengthAwarePaginator
    {
        $perPage = $this->getNumberPerPage($request);
        
        return $query->paginate($perPage)->appends([
                    'number' => $perPage,
                    'page' => $this->getCurrentPageBySerialNumber($request, $serialNumber),
                ]);
    }
    
    /**
     * Возвращает номер актёра в списке/таблице актёров
     * 
     * @param int $actorId - id актёра
     * @return int
     */
    private function getSerialNumberOfTheActorInTheList(int $actorId): int
    {
        return DB::selectOne(<<<SQL
                WITH _ AS (
                    SELECT 
                        id,
                        row_number() OVER(ORDER BY first_name, last_name) AS n
                    FROM dvd.actors
                )
                SELECT
                    n
                FROM _
                WHERE id = :actorId
            SQL, [
                'actorId' => $actorId
            ])->n;
    }
}
