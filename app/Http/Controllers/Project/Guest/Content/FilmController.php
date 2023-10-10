<?php

namespace App\Http\Controllers\Project\Guest\Content;

use App\Http\Controllers\Controller;
use App\Models\Dvd\Film;
use App\Providers\RouteServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FilmController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Film::with('language:id,name')
                    ->select('id', 'title', 'description', 'language_id')
                    ->when($request->title, function (Builder $query, string $title) {
                        $query->where('title', 'ILIKE', "%$title%");
                    })
                    ->when($request->description, function (Builder $query, string $description) {
                        $query->where('description', 'ILIKE', "%$description%");
                    });
   
        // Число фильмов на странице
        $perPage = $request->number ?? RouteServiceProvider::PAGINATE_DEFAULT_PER_PAGE;
        
        return Inertia::render('Guest/Films', [
                'films' => $query->orderBy('title')->paginate($perPage)->appends([
                    'number' => $perPage,
                    'title' => $request->title,
                    'description' => $request->description
                ])
            ]);
    }
}
