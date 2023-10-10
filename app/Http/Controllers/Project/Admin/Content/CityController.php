<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpenWeather\CityRequest;
use App\Models\Thesaurus\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Cities', [
            'cities' => City::select('id', 'name', 'open_weather_id', 'timezone_id')
                ->with('timezone:id,name')
                ->orderBy('name')
                ->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CityRequest $request): RedirectResponse
    {
        // Создаём новую запись в таблице 'thesaurus.cities'
        $city = new City();
        $city->name = $request->name;
        $city->open_weather_id = $request->open_weather_id;
        $city->save();
        
        return redirect('admin/cities');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string']
        ], [
            'name.required' => trans("city.cityName.required"),
            'name.string' => trans("city.cityName.string"),
        ])->validated();
        
        $city = City::find($id);
        $city->name = $request->name;
        $city->save();
        
        return redirect('admin/cities');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        City::find($id)->delete();
        
        return redirect('admin/cities');
    }
    
    public function setTimezone(int $city_id, int $timezone_id): RedirectResponse
    {
        
        $city = City::find($city_id);
        $city->timezone_id = $timezone_id ?: null;
        $city->save();
        
        return redirect('admin/cities');
    }
}
