<?php

namespace App\Http\Controllers\Project\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dvd\Filters\FilmFilterRequest;
use App\Providers\RouteServiceProvider;
use App\Services\Database\Person\UserService;
use App\Support\Pagination\Urls\FilmUrls;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    public function __construct(
        private UserService $userService,
        private FilmUrls $filmUrls
    ) {
    }
    
    /**
     * Аутентифицированный пользователь становится админом
     * 
     * @param FilmFilterRequest $request
     * @return RedirectResponse
     */
    public function create(FilmFilterRequest $request): RedirectResponse
    {
        $this->userService->assignAdmin($request->user()->id);
        
        return redirect($this->filmUrls->getUrlWithPaginationOptionsByRequest(
                    RouteServiceProvider::URL_AUTH_USERFILMS,
                    $request->getPaginatorDto(),
                    $request->getFilmFilterDto()
                ));
    }
    
    /**
     * Админ отказывается от прав
     * 
     * @param FilmFilterRequest $request
     * @return RedirectResponse
     */
    public function destroy(FilmFilterRequest $request): RedirectResponse
    {
        $this->userService->depriveAdmin($request->user()->id);
        
        return redirect($this->filmUrls->getUrlWithPaginationOptionsByRequest(
                    RouteServiceProvider::URL_AUTH_USERFILMS,
                    $request->getPaginatorDto(),
                    $request->getFilmFilterDto()
                ));
    }
}
