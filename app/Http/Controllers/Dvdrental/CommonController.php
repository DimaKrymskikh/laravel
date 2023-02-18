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
        return Inertia::render('Home', [
            'title' => 'Главная страница',
            'isGuest' => !Auth::check()
        ]);
    }
    
    public function catalog(): Response
    {
        return Inertia::render('Films', [
                'title' => 'Каталог',
                'films' => Film::with('language:id,name')->select('id', 'title', 'description', 'language_id')->orderBy('title')->paginate(20),
                'isGuest' => !Auth::check()
            ]);
    }
}
