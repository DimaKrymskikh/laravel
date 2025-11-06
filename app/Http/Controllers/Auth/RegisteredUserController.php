<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Providers\RouteServiceProvider;
use App\Services\Database\Person\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function __construct(
        private UserService $userService,
    ) {
    }
    
    /**
     * Отрисовка формы регистрации
     * 
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Guest/Register');
    }
    
    /**
     * Регистрация пользователя
     * 
     * @param RegisterRequest $request
     * @return RedirectResponse
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = $this->userService->create($request->getDto());

        event(new Registered($user));

        Auth::login($user, $request->input('is_remember'));

        return redirect(RouteServiceProvider::HOME);
    }
    
    /**
     * Удаление аккаунта
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function remove(Request $request): RedirectResponse
    {
        $this->userService->remove($request->user());
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        
        return redirect(RouteServiceProvider::HOME);
    }
}
