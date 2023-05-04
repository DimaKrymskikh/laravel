<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Проверяет запрос для получения ссылки на смену пароля
 */
class PasswordResetRequest extends FormRequest
{
    /**
     * Проверка авторизации пользователя не нужна.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * В запросе на смену пароля присутствует только email
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }
    
    /**
     * 
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.required' => trans("auth.required.email"),
            'email.email' => trans("auth.email"),
        ];
    }
}
