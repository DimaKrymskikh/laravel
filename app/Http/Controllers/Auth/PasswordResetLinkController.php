<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Отрисовка формы с полем email для получения ссылки на смену пароля.
     * 
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Guest/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Отправляет email со ссылкой на смену пароля 
     * 
     * @param PasswordResetRequest $request
     * @return RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(PasswordResetRequest $request): RedirectResponse
    {
        // Обрабатывается запрос и при успешной обработке адреса эл. почты пользователю отправляется email
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Если email отправлен, на странице с формой появится подтверждение об отправке email
        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', trans($status));
        }

        // Если email не отправлен, на странице с формой появится сообщение об ошибке
        throw ValidationException::withMessages([
            'email' => trans($status),
        ]);
    }
}
