<?php

namespace App\Http\Requests\Auth;

use App\Rules\Auth\MixedCasePassword;
use App\Rules\Auth\NumbersPassword;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Проверяет запрос, который задаёт новый пароль пользователя
 */
class NewPasswordRequest extends FormRequest
{
    /**
     * Проверка авторизации пользователя не нужна.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'token' => 'required',
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(6)->rules([new MixedCasePassword, new NumbersPassword])],
        ];
    }
    
    /**
     * 
     * @return array
     */
    public function messages(): array
    {
        return [
            'token.required' => trans("auth.required.token"),
            'email.required' => trans("auth.required.email"),
            'email.email' => trans("auth.email"),
            'password.required' => trans("auth.required.password"),
            'password.confirmed' => trans("auth.confirmed.password"),
            'password.min' => trans("auth.min.password"),
        ];
    }
}
