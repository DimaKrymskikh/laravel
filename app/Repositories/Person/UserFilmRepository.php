<?php

namespace App\Repositories\Person;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;
use Illuminate\Database\Query\JoinClause;

final class UserFilmRepository implements UserFilmRepositoryInterface
{
    public function exists(int $userId, int $filmId): bool
    {
        return UserFilm::where('user_id', $userId)
                ->where('film_id', $filmId)
                ->exists();
    }
    
    public function save(UserFilm $userFilm, int $userId, int $filmId): void
    {
        $userFilm->user_id = $userId;
        $userFilm->film_id = $filmId;
        $userFilm->save();
    }
    
    public function delete(int $userId, int $filmId): void
    {
        UserFilm::where('user_id', '=', $userId)
            ->where('film_id', '=', $filmId)
            ->delete();
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
