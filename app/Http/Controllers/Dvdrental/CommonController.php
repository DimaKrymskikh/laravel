<?php

namespace App\Http\Controllers\Dvdrental;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use App\Models\Dvd\Film;

class CommonController extends Controller
{
    public function home(): Response
    {
        return Inertia::render((Auth::check() ? 'Auth/' : 'Guest/') . 'Home');
    }
    
    public function catalog(): Response
    {
        $view = (Auth::check() ? 'Auth/' : 'Guest/') . 'Catalog';
        
        return Inertia::render($view, [
                'films' => Film::with('language:id,name')->select('id', 'title', 'description', 'language_id')->orderBy('title')->paginate(20),
            ]);
    }
}
