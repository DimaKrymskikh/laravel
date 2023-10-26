<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\Events\AddFilm;
use App\Events\RemoveFilm;
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

class UserFilmsController extends Controller
{
    use Films, Url;
    
    /**
     * Отрисовывает страницу аккаунта
     * 
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Account/UserFilms', [
            'films' => $this->getUserFilmsList($request),
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
    
    /**
     * Удаляет фильм с film_id из коллекции пользователя.
     * 
     * @param Request $request
     * @param string $film_id
     * @return RedirectResponse
     * @throws type
     */
    public function removeFilm(Request $request, string $film_id): RedirectResponse
    {
        $query = UserFilm::where('user_id', '=', Auth::id())
                ->where('film_id', '=', $film_id);
        
        // Получаем данные строки, которую хотим удалить.
        $userFilm = $query->first();
        
        // Удаление фильма с film_id из коллекции пользователя.
        if ($query->delete()) {
            // При успешном удалении фильма пользователь получает оповещение
            event(new RemoveFilm($userFilm));
        }
        
        return redirect($this->getUrl('userfilms', [
            'page' => $request->page,
            'number' => $request->number,
            'title' => $request->title,
            'description' => $request->description
        ]));
    }
    
    public function show(int $film_id): Response
    {
        return Inertia::render('Auth/FilmCard', [
            'film' => $this->getFilmCard($film_id),
            'user' => Auth::getUser()
        ]);
    }
}
