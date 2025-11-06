<?php

namespace App\Modifiers\Dvd\Films;

use App\Models\Dvd\Film;
use App\Models\Dvd\FilmActor;
use App\Modifiers\Modifiers;
use Illuminate\Support\Facades\DB;

final class FilmModifiers extends Modifiers implements FilmModifiersInterface
{
    public function delete(int $filmId): void
    {
        DB::transaction(function () use ($filmId) {
            FilmActor::where('film_id', $filmId)->delete();
            Film::where('id', $filmId)->delete();
        });
    }
}
