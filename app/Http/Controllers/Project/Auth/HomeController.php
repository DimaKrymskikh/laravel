<?php

namespace App\Http\Controllers\Project\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Auth/Home', [
            'user' => Auth::getUser()
        ]);
    }
}
