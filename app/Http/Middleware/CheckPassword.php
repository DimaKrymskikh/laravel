<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CheckPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Если пароль введён неверно, бросается исключение
        if (! Hash::check($request->password, Auth::getUser()->password)) {
            throw ValidationException::withMessages([
                'password' => trans('user.password.wrong')
            ]);
        }
        return $next($request);
    }
}
