<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpenWeather\CityRequest;
use App\Providers\RouteServiceProvider;
use App\Services\Database\Thesaurus\CityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class CityController extends Controller
{
    public function __construct(
        private CityService $cityService,
    ) {
        $this->middleware('check.password')->only('destroy');
    }
    
    /**
     * В админской части отрисовывает таблицу городов.
     * 
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Cities', [
            'cities' => $this->cityService->getAllCitiesList(),
        ]);
    }

    /**
     * Создаёт новую запись в таблице 'thesaurus.cities'.
     * 
     * @param CityRequest $request
     * @return RedirectResponse
     */
    public function store(CityRequest $request): RedirectResponse
    {
        $this->cityService->create($request->name, $request->open_weather_id);
        
        return redirect(RouteServiceProvider::URL_ADMIN_CITIES);
    }

    /**
     * Изменяет название города.
     * 
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string']
        ], [
            'name.required' => trans("city.cityName.required"),
            'name.string' => trans("city.cityName.string"),
        ])->validated();
        
        $this->cityService->update($id, 'name', $request->name);
        
        return redirect(RouteServiceProvider::URL_ADMIN_CITIES);
    }

    /**
     * Удаляет город из таблицы 'thesaurus.cities'.
     * 
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->cityService->delete($id);
        
        return redirect(RouteServiceProvider::URL_ADMIN_CITIES);
    }
    
    /**
     * Изменяет/Задаёт временной пояс города.
     * 
     * @param int $cityId
     * @param int $timezoneId
     * @return RedirectResponse
     */
    public function setTimezone(int $cityId, int $timezoneId): RedirectResponse
    {
        $this->cityService->update($cityId, 'timezone_id', $timezoneId);
        
        return redirect(RouteServiceProvider::URL_ADMIN_CITIES);
    }
}
