<?php

namespace App\Http\Controllers\Dvdrental;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;

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
    
    public function addFilm(Request $request, string $film_id): RedirectResponse
    {
        // Проверка наличия в таблице 'person.users_films' пары первичных ключей (user_id, film_id)
        if (
            UserFilm::where('user_id', '=', Auth::id())
                ->where('film_id', '=', $film_id)
                ->exists()
        ) {
            // Если пара существует, выбрасываем исключение
            $film = Film::find($film_id);
            throw ValidationException::withMessages([
                'message' => trans("user.film.message", [
                    'film' => $film->title
                ]),
            ]);
        }
        
        // Новую пару записываем в таблицу 'person.users_films'
        $userFilm = new UserFilm;
        $userFilm->user_id = Auth::id();
        $userFilm->film_id = $film_id;
        $userFilm->save();
        
        return redirect("/catalog?page=$request->page");
    }
}
