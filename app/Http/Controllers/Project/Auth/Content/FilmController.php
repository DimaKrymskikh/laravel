<?php

namespace App\Http\Controllers\Project\Auth\Content;

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
        return Inertia::render('Auth/Films', [
                'films' => $this->films->getFilmsListWithAvailable($request),
                'user' => $request->user()
            ]);
    }
}
