<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FallbackController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $file = 'Guest/Fallback';
        
        if($user && !$user->is_admin) {
            $file = 'Auth/Fallback';
        }
        
        if($user && $user->is_admin) {
            $file = 'Admin/Fallback';
        }
        
        return Inertia::render($file, [
                'user' => $user
        ]);
    }
}
