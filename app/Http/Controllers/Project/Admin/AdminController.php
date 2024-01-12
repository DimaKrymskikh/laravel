<?php

namespace App\Http\Controllers\Project\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Repositories\Dvd\FilmRepository;
use App\Support\Pagination\Url;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private Url $url;

    public function __construct()
    {
        $this->url = new Url(FilmRepository::ADDITIONAL_PARAMS_IN_URL);
    }
    
    /**
     * Аутентифицированный пользователь становится админом
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function create(Request $request): RedirectResponse
    {
        User::where('id', $request->user()->id)
            ->update(['is_admin' => true]);
        
        return redirect($this->url->getUrlByRequest(RouteServiceProvider::URL_AUTH_USERFILMS, $request));
    }
    
    /**
     * Админ отказывается от прав
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        User::where('id', $request->user()->id)
            ->update(['is_admin' => false]);
        
        return redirect($this->url->getUrlByRequest(RouteServiceProvider::URL_AUTH_USERFILMS, $request));
    }
}
