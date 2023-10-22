<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\Events\RemoveFilm;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Url;
use App\Http\Extraction\Dvd\Films;
use App\Models\Person\UserFilm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
}
