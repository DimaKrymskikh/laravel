<?php

namespace App\Repositories\Dvd;

use App\Contracts\Repositories\ListItem;
use App\Models\Dvd\Actor;
use App\Support\Pagination\Paginator;
use App\Support\Pagination\RequestGuard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ActorRepository implements ListItem
{
    use Paginator;
    
    public const ADDITIONAL_PARAMS_IN_URL = ['name'];
    
    private RequestGuard $guard;
    
    public function __construct()
    {
        $this->guard = new RequestGuard(self::ADDITIONAL_PARAMS_IN_URL);
    }

    private function queryCommonActorsList(Request $request): Builder
    {
        return Actor::select(
                'id',
                'first_name',
                'last_name',
                DB::raw('row_number() OVER(ORDER BY first_name, last_name) AS n')
            )
            ->filter($request)
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
    public function getCommonActorsList(Request $request, bool $isPagination = true): LengthAwarePaginator | Collection
    {
        $query =  $this->queryCommonActorsList($request);
                
        return $isPagination ? $this->setPagination($query, $request, $this->guard) : $query->get();
    }
    
    /**
     * Возвращает номер актёра в списке/таблице актёров с фильтрами и с сортировкой
     * 
     * @param int $actorId - id актёра
     * @return int
     */
    public function getSerialNumberOfItemInList(Request $request, int $actorId): int
    {
        $actor = $this->queryCommonActorsList($request)->get()->find($actorId);
        
        return $actor ? $actor->n : RequestGuard::DEFAULT_SERIAL_NUMBER;
    }
    
    /**
     * Возвращает общее число актёров в списке актёров с фильтрами и с сортировкой
     * 
     * @param Request $request
     * @return int
     */
    public function getNumberOfItemsInList(Request $request): int
    {
        return $this->queryCommonActorsList($request)->get()->count();
    }
}
