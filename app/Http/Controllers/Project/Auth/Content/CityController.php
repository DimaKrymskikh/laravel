<?php

namespace App\Http\Controllers\Project\Auth\Content;

use App\Events\AddCityInWeatherList;
use App\Events\RemoveCityFromWeatherList;
use App\Http\Controllers\Controller;
use App\Services\Database\Person\UserCityService;
use App\Services\Database\Thesaurus\CityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CityController extends Controller
{
    public function __construct(
            private UserCityService $userCityService,
            private CityService $cityService,
    ) {
    }
    
    /**
     * Отрисовывает список городов с указанием принадлежности списку погоды пользователя
     * 
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Auth/Cities', [
            'user' => $request->user(),
            'cities' => $this->cityService->getListWithAvailableByUserId($request->user()->id)
        ]);
    }
    
    /**
     * Добавляет город в список просмотра погоды пользователя
     * 
     * @param Request $request
     * @param string $cityId
     * @return RedirectResponse
     */
    public function addCity(Request $request, string $cityId): RedirectResponse
    {
        $this->userCityService->create($request->user()->id, $cityId);
        
        event(new AddCityInWeatherList($request->user()->id, $cityId, $this->cityService));
        
        return redirect('cities');
    }
    
    /**
     * Удаляет город из списка просмотра погоды пользователя
     * 
     * @param Request $request
     * @param string $cityId
     * @return RedirectResponse
     */
    public function removeCity(Request $request, string $cityId): RedirectResponse
    {
        $this->userCityService->delete($request->user()->id, $cityId);
        
        event(new RemoveCityFromWeatherList($request->user()->id, $cityId, $this->cityService));
        
        return redirect('userweather');
    }
}
