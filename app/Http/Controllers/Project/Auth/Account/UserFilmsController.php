<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\CommandHandlers\Database\Dvd\Films\FilmsListForPageCommandHandler;
use App\Events\AddFilmInUserList;
use App\Events\RemoveFilmFromUserList;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dvd\Filters\FilmFilterRequest;
use App\Providers\RouteServiceProvider;
use App\Services\Database\Dvd\FilmService;
use App\Support\Pagination\Urls\Films\FilmUrls;
use App\Support\Pagination\Urls\Films\UserFilmUrls;
use App\Services\Database\Person\UserFilmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UserFilmsController extends Controller
{
    public function __construct(
        private FilmsListForPageCommandHandler $filmHandler,
        private FilmService $filmService,
        private UserFilmService $userFilmService,
        private FilmUrls $filmUrls,
        private UserFilmUrls $userFilmUrls,
    ) {
    }
    
    /**
     * Отрисовывает страницу аккаунта
     * 
     * @param FilmFilterRequest $request
     * @return Response
     */
    public function create(FilmFilterRequest $request): Response
    {
        return Inertia::render('Auth/Account/UserFilms', [
            'films' => $this->filmHandler->handle($request->getPaginatorDto(), $request->getFilmFilterDto(), $request->user()->id),
            'user' => $request->user()
        ]);
    }
    
    /**
     * Добавляет фильм с filmId в коллекцию пользователя
     * 
     * @param FilmFilterRequest $request
     * @param int $filmId
     * @return RedirectResponse
     */
    public function addFilm(FilmFilterRequest $request, int $filmId): RedirectResponse
    {
        $user = $request->user();
        
        $this->userFilmService->create($user->id, $filmId);
        // Если запись была успешной, пользователь получает оповещение
        event(new AddFilmInUserList($user->id, $filmId, $this->filmService));
        
        return redirect($this->filmUrls->getUrlWithPaginationOptionsByRequest(
                    RouteServiceProvider::URL_AUTH_FILMS,
                    $request->getPaginatorDto(),
                    $request->getFilmFilterDto()
                ));
    }
    
    /**
     * Удаляет фильм с filmId из коллекции пользователя.
     * 
     * @param FilmFilterRequest $request
     * @param int $filmId
     * @return RedirectResponse
     */
    public function removeFilm(FilmFilterRequest $request, int $filmId): RedirectResponse
    {
        $user = $request->user();
        
        // Удаление фильма с filmId из коллекции пользователя.
        $this->userFilmService->delete($user->id, $filmId);
        // При успешном удалении фильма пользователь получает оповещение
        event(new RemoveFilmFromUserList($user->id, $filmId, $this->filmService));
        
        return redirect($this->userFilmUrls->getUrlWithPaginationOptionsAfterRemovingFilm(
                    RouteServiceProvider::URL_AUTH_USERFILMS,
                    $request->getPaginatorDto(),
                    $request->getFilmFilterDto(),
                    $user->id
                ));
    }
    
    /**
     * Отрисовывает карточку фильма
     * 
     * @param int $filmId
     * @return Response
     */
    public function show(int $filmId): Response
    {
        return Inertia::render('Auth/FilmCard', [
            'film' => $this->filmService->getFilmCard($filmId),
            'user' => Auth::getUser()
        ]);
    }
}
