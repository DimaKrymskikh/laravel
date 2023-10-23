<?php

namespace App\Http\Controllers\Project\Auth\Content;

use App\Http\Controllers\Controller;
use App\Http\Extraction\Dvd\Films;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FilmController extends Controller
{
    use Films;
    
    public function index(Request $request): Response
    {
        return Inertia::render('Auth/Films', [
                'films' => $this->getFilmsListWithAvailable($request),
                'user' => $request->user()
            ]);
    }
}
