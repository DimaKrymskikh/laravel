<?php

namespace App\Http\Controllers\Project\Guest\Content;

use App\Http\Controllers\Controller;
use App\Services\Database\Thesaurus\CityService;
use Inertia\Inertia;
use Inertia\Response;

class CityController extends Controller
{
    public function __construct(
            private CityService $cityService,
    ) {
    }
    
    /**
     * Для гостя отрисовывает таблицу городов.
     * 
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Guest/Cities', [
            'cities' => $this->cityService->getAllCitiesList()
        ]);
    }
}
