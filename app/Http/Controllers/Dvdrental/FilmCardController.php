<?php

namespace App\Http\Controllers\Dvdrental;

use App\Http\Controllers\Controller;
use App\Http\Extraction\Dvd\Films;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FilmCardController extends Controller
{
    use Films;
    
    public function create(int $film_id): Response
    {
        return Inertia::render('Auth/FilmCard', [
            'film' => $this->getFilmCard($film_id),
            'user' => Auth::getUser()
        ]);
    }
}
