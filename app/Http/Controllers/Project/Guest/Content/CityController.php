<?php

namespace App\Http\Controllers\Project\Guest\Content;

use App\Models\Thesaurus\City;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CityController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Guest/Cities', [
            'cities' => City::select('id', 'name', 'timezone_id')
                ->with('timezone:id,name')
                ->orderBy('name')
                ->get()
        ]);
    }
}
