<?php

namespace App\Queries\Person\UsersFilms;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;
use Illuminate\Database\Query\JoinClause;

final class UserFilmQueries implements UserFilmQueriesInterface
{
    public function exists(int $userId, int $filmId): bool
    {
        return UserFilm::where('user_id', $userId)
                ->where('film_id', $filmId)
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
