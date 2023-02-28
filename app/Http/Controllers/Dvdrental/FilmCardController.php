<?php

namespace App\Http\Controllers\Dvdrental;

use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use App\Models\Dvd\Film;

class FilmCardController extends Controller
{
    public function create(string $film_id): Response
    {
        return Inertia::render('Auth/FilmCard', [
            'film' => Film::with([
                'language:id,name',
                'actors:id,first_name,last_name'
            ])
            ->select('id', 'title', 'description', 'release_year', 'language_id')
            ->where('id', '=', $film_id)
            ->first()
        ]);
    }
}
