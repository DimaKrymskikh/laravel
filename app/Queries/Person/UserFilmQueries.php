<?php

namespace App\Queries\Person;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;
use App\Services\Database\Person\Dto\UserFilmDto;
use App\Services\DatabaseQueryInterface;
use Illuminate\Database\Query\JoinClause;

class UserFilmQueries implements DatabaseQueryInterface
{
    public function exists(UserFilmDto $dto): bool
    {
        return UserFilm::where('user_id', $dto->userId)
                ->where('film_id', $dto->filmId)
                ->exists();
    }
    
    public function count(FilmFilterDto $dto, int $userId): int
    {
        return Film::join('person.users_films', function(JoinClause $join) use ($userId) {
                    $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                        ->where('person.users_films.user_id', $userId);
                })
                ->filter($dto)
                ->count();
    }
}
