<?php

namespace App\Http\Controllers\Project\Guest;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Guest/Home');
    }
}
