<?php

namespace App\Http\Controllers\Project\Guest\Content;

use App\Http\Controllers\Controller;
use App\Repositories\Dvd\FilmRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FilmController extends Controller
{
    public function __construct(
        private FilmRepository $films,
    )
    {}
    
    public function index(Request $request): Response
    {
        return Inertia::render('Guest/Films', [
                'films' => $this->films->getCommonFilmsList($request)
            ]);
    }
}
