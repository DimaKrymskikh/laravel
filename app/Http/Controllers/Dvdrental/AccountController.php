<?php

namespace App\Http\Controllers\Dvdrental;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use App\Models\Dvd\Film;

class AccountController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Account', [
            'films' => Film::with('language:id,name')
                ->join('person.users_films', function(JoinClause $join) {
                    $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                        ->where('person.users_films.user_id', '=', Auth::id());
                })
                ->select('id', 'title', 'description', 'language_id')
                ->orderBy('title')->paginate(20),
            'user' => Auth::getUser()
        ]);
    }
}
