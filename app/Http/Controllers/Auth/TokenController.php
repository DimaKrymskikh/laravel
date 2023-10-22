<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TokenController extends Controller
{
    /**
     * Перенаправляет пользователя на главную страницу,
     * если он в браузере обновит страницу токена
     * 
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        return redirect(RouteServiceProvider::HOME);
    }
    
    /**
     * Генерирует токен и отдаёт его в аккаунт пользователя
     * 
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $user = $request->user();
        // Удаляем существующие токены пользователя
        $user->tokens()->delete();
        
        return Inertia::render('Auth/Token', [
            'token' => $user->createToken("api token")->plainTextToken,
            'user' => $user
        ]);
    }
}
