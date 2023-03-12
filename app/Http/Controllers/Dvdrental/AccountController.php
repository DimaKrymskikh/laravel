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
use App\Providers\RouteServiceProvider;

class AccountController extends Controller
{
    public function create(Request $request): Response
    {
        // Число фильмов на странице
        $perPage = $request->number ?? RouteServiceProvider::PAGINATE_DEFAULT_PER_PAGE;
        
        return Inertia::render('Auth/Account', [
            'films' => Film::with('language:id,name')
                ->join('person.users_films', function(JoinClause $join) {
                    $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                        ->where('person.users_films.user_id', '=', Auth::id());
                })
                ->select('id', 'title', 'description', 'language_id')
                ->orderBy('title')->paginate($perPage)->appends(['number' => $perPage]),
            'user' => Auth::getUser()
        ]);
    }
    
    /**
     * Добавляет фильм с film_id в коллекцию пользователя
     * @param Request $request
     * @param string $film_id
     * @return RedirectResponse
     * @throws type
     */
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
    
    /**
     * Удаляет фильм с film_id из коллекции пользователя
     * @param Request $request
     * @param string $film_id
     * @return RedirectResponse
     * @throws type
     */
    public function removeFilm(Request $request, string $film_id): RedirectResponse
    {
        // Удаление фильма с film_id из коллекции пользователя
        UserFilm::where('user_id', '=', Auth::id())
                ->where('film_id', '=', $film_id)
                ->delete();
        
        return redirect("/account?page=$request->page");
    }
}
