<?php

namespace App\Http\Controllers\Project\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Отрисовка "Домашней страницы админа"
     * 
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Home', []);
    }
}
