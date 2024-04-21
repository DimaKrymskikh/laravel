<?php

namespace App\Http\Controllers\Project\Auth\Content;

use App\Events\AddCityInWeatherList;
use App\Events\RemoveCityFromWeatherList;
use App\Http\Controllers\Controller;
use App\Models\Person\UserCity;
use App\Models\Thesaurus\City;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CityController extends Controller
{
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
            'cities' => City::select('id', 'name', 'timezone_id')
                ->with('timezone:id,name')
                ->leftJoin('person.users_cities', function(JoinClause $join) {
                    $join->on('person.users_cities.city_id', 'thesaurus.cities.id')
                        ->where('person.users_cities.user_id', Auth::id());
                })
                ->selectRaw('coalesce (person.users_cities.user_id::bool, false) AS "isAvailable"')
                ->orderBy('name')
                ->get()
        ]);
    }
    
    /**
     * Добавляет город в список просмотра погоды пользователя
     * 
     * @param Request $request
     * @param string $city_id
     * @return RedirectResponse
     */
    public function addCity(Request $request, string $city_id): RedirectResponse
    {
        // Новую пару записываем в таблицу 'person.users_cities'
        $userCity = new UserCity();
        $userCity->user_id = $request->user()->id;
        $userCity->city_id = $city_id;
        
        if($userCity->save()){
            event(new AddCityInWeatherList($request->user()->id, $city_id));
        }
        
        return redirect('cities');
    }
    
    /**
     * Удаляет город из списка просмотра погоды пользователя
     * 
     * @param Request $request
     * @param string $city_id
     * @return RedirectResponse
     */
    public function removeCity(Request $request, string $city_id): RedirectResponse
    {
        $query = UserCity::where('user_id', '=', $request->user()->id)
                ->where('city_id', '=', $city_id);
        
        if($query->delete()) {
            event(new RemoveCityFromWeatherList($request->user()->id, $city_id));
        }
        
        return redirect('userweather');
    }
}
