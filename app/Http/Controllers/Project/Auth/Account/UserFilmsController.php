<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\Events\AddFilmInUserList;
use App\Events\RemoveFilmFromUserList;
use App\Http\Controllers\Controller;
use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;
use App\Providers\RouteServiceProvider;
use App\Repositories\Dvd\FilmRepository;
use App\Support\Pagination\Url;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UserFilmsController extends Controller
{
    private Url $url;

    public function __construct(
        private FilmRepository $films,
    )
    {
        $this->url = new Url(FilmRepository::ADDITIONAL_PARAMS_IN_URL);
    }
    
    /**
     * Отрисовывает страницу аккаунта
     * 
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Account/UserFilms', [
            'films' => $this->films->getUserFilmsList($request),
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
    public function addFilm(Request $request, int $film_id): RedirectResponse
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
            event(new AddFilmInUserList($userFilm));
        }
        
        return redirect($this->url->getUrlByRequest(RouteServiceProvider::URL_AUTH_FILMS, $request));
    }
    
    /**
     * Удаляет фильм с film_id из коллекции пользователя.
     * 
     * @param Request $request
     * @param string $film_id
     * @return RedirectResponse
     * @throws type
     */
    public function removeFilm(Request $request, int $film_id): RedirectResponse
    {
        $query = UserFilm::where('user_id', '=', Auth::id())
                ->where('film_id', '=', $film_id);
        
        // Получаем данные строки, которую хотим удалить.
        $userFilm = $query->first();
        
        // Удаление фильма с film_id из коллекции пользователя.
        if ($query->delete()) {
            // При успешном удалении фильма пользователь получает оповещение
            event(new RemoveFilmFromUserList($userFilm));
        }
        
        return redirect($this->url->getUrlAfterRemovingItem(RouteServiceProvider::URL_AUTH_USERFILMS, $request, $this->films));
    }
    
    public function show(int $film_id): Response
    {
        return Inertia::render('Auth/FilmCard', [
            'film' => $this->films->getFilmCard($film_id),
            'user' => Auth::getUser()
        ]);
    }
}
