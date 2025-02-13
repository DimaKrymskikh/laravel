<?php

namespace App\Repositories\Dvd;

use App\DataTransferObjects\Database\Dvd\FilmDto;
use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Models\Dvd\Film;
use App\Models\Dvd\FilmActor;
use App\Providers\BindingInterfaces\RepositoriesProvider;
use Illuminate\Contracts\Database\Eloquent\Builder as ContractsBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

final class FilmRepository implements FilmRepositoryInterface
{
    public function exists(int $filmId): bool
    {
        return Film::where('id', $filmId)->exists();
    }
    
    public function save(Film $film, FilmDto $dto): void
    {
        $film->title = $dto->title;
        $film->description = $dto->description;
        $film->release_year = $dto->releaseYear->value;
        $film->save();
    }
    
    public function saveField(Film $film, string $field, ?string $value): void
    {
        $film->$field = $value;
        $film->save();
    }
    
    public function delete(int $filmId): void
    {
        DB::transaction(function () use ($filmId) {
            FilmActor::where('film_id', $filmId)->delete();
            Film::where('id', $filmId)->delete();
        });
    }
    
    public function count(FilmFilterDto $dto): int
    {
        return Film::filter($dto)->count();
    }
    
    public function getById(int $filmId): Film
    {
        return Film::find($filmId);
    }
    
    public function getByIdWithActors(int $filmId): Film
    {
        return Film::with([
                'language:id,name',
                'actors' => function (ContractsBuilder $query) {
                    $query->select('id', 'first_name', 'last_name')
                        ->orderBy('first_name')
                        ->orderBy('last_name');
                }
            ])
            ->select('id', 'title', 'description', 'release_year', 'language_id')
            ->find($filmId);
    }
    
    public function getList(FilmFilterDto $dto): Collection
    {
        return $this->queryList($dto)->limit(RepositoriesProvider::DEFAULT_LIMIT)->get();
    }
    
    public function getListForPage(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): LengthAwarePaginator
    {
        $query = $this->queryList($filmFilterDto);
        
        return $this->paginate($query, $paginatorDto, $filmFilterDto);
    }
    
    public function getListByUserIdForPage(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto, int $userId): LengthAwarePaginator
    {
        $query = $this->queryListByUserId($filmFilterDto, $userId);
        
        return $this->paginate($query, $paginatorDto, $filmFilterDto);
    }
    
    public function getListForPageWithActors(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): LengthAwarePaginator
    {
        $query = $this->queryListWithActors($filmFilterDto);
        
        return $this->paginate($query, $paginatorDto, $filmFilterDto);
    }
    
    public function getListForPageWithAvailable(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto, int $userId): LengthAwarePaginator
    {
        $query = $this->queryListWithAvailable($filmFilterDto, $userId);
        
        return $this->paginate($query, $paginatorDto, $filmFilterDto);
    }
    
    public function getRowNumbers(): Collection
    {
        return Film::select(
                'id',
                DB::raw('row_number() OVER(ORDER BY title) AS n')
            )
            ->orderBy('title')
            ->get();
    }
    
    private function queryList(FilmFilterDto $dto): Builder
    {
        return Film::with('language:id,name')
                ->select('id', 'title', 'description', 'language_id', 'release_year')
                ->filter($dto)
                ->orderBy('title');
    }
    
    private function queryListByUserId(FilmFilterDto $dto, int $userId): Builder
    {
        return Film::with('language:id,name')
                ->join('person.users_films', function(JoinClause $join) use ($userId) {
                    $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                        ->where('person.users_films.user_id', $userId);
                })
                ->select('id', 'title', 'description', 'language_id')
                ->filter($dto)
                ->orderBy('title');
    }
    
    private function queryListWithActors(FilmFilterDto $dto): Builder
    {
        return Film::with('language:id,name')
                ->leftJoin('dvd.films_actors', 'dvd.films_actors.film_id', '=', 'dvd.films.id')
                ->leftJoin('dvd.actors', 'dvd.actors.id', '=', 'dvd.films_actors.actor_id')
                ->select(
                        'dvd.films.id',
                        'dvd.films.title',
                        'dvd.films.description',
                        'dvd.films.language_id',
                        'dvd.films.release_year',
                        DB::raw(<<<SQL
                                COALESCE(NULLIF(
                                    STRING_AGG(CONCAT(dvd.actors.first_name, ' ', dvd.actors.last_name), ', '), ' '
                                ), 'Актёры не добавлены') AS "actorsList"
                            SQL)
                    )
                ->filter($dto)
                ->groupBy('dvd.films.id')
                ->orderBy('dvd.films.title');
    }
    
    private function queryListWithAvailable(FilmFilterDto $dto, int $userId): Builder
    {
        return Film::with('language:id,name')
                    ->leftJoin('person.users_films', function(JoinClause $join) use ($userId) {
                        $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                            ->where('person.users_films.user_id', $userId);
                    })
                ->select('id', 'title', 'description', 'language_id')
                ->selectRaw('coalesce (person.users_films.user_id::bool, false) AS "isAvailable"')
                ->filter($dto)
                ->orderBy('title');
    }
    
    private function paginate(Builder $query, PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): LengthAwarePaginator
    {
        $perPage = $paginatorDto->perPage->value;
                
        return $query
                ->paginate($perPage)
                ->appends([
                    'number' => $perPage,
                    'title_filter' => $filmFilterDto->title,
                    'description_filter' => $filmFilterDto->description,
                    'release_year_filter' => (string) $filmFilterDto->releaseYear,
                ]);
    }
}
