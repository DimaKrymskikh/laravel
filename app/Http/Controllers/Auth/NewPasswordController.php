<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\NewPasswordRequest;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    /**
     * Отображение страницы сброса пароля.
     * 
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Guest/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    /**
     * Обработка входящего запроса на новый пароль.
     * 
     * @param NewPasswordRequest $request
     * @return RedirectResponse
     * @throws type
     */
    public function store(NewPasswordRequest $request): RedirectResponse
    {
        // Выполняется сброс пароля пользователя.
        // В случае успеха пароль пользователя сохраняется в базе данных, 
        // и пользователю отправляется email об успешном обновлении пароля.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
// Функционал "Запомнить меня" не реализован                    
//                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Если пароль был успешно сброшен, пользователь перенаправляется на страницу входа
        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', trans($status));
        }

        // Если пароль не был сброшен, на станице запроса на новый пароль появится сообщение об ошибке
        throw ValidationException::withMessages([
            'email' => trans($status),
        ]);
    }
}
