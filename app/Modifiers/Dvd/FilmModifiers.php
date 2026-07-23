<?php

namespace App\Modifiers\Dvd;

use App\Models\Dvd\Film;
use App\Models\Dvd\FilmActor;
use App\Modifiers\Modifiers;
use Illuminate\Support\Facades\DB;

class FilmModifiers extends Modifiers
{
    public function delete(int $filmId): void
    {
        DB::transaction(function () use ($filmId) {
            FilmActor::where('film_id', $filmId)->delete();
            Film::where('id', $filmId)->delete();
        });
    }
}
