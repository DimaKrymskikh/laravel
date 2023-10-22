<?php

namespace App\Http\Controllers\Project\Auth\Content;

use App\Events\AddFilm;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Url;
use App\Http\Extraction\Dvd\Films;
use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FilmController extends Controller
{
    use Films, Url;
    
    public function index(Request $request): Response
    {
        return Inertia::render('Auth/Films', [
                'films' => $this->getFilmsListWithAvailable($request),
                'user' => $request->user()
            ]);
    }
    
    /**
     * Добавляет фильм с film_id в коллекцию пользователя
     * 
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
        
        // Если запись была успешной, пользователь получает оповещение
        if ($userFilm->save()) {
            event(new AddFilm($userFilm));
        }
        
        return redirect($this->getUrl('/films', [
            'page' => $request->page,
            'number' => $request->number,
            'title' => $request->title,
            'description' => $request->description
        ]));
    }
}
