<?php

namespace App\Http\Requests\Auth;

use App\Rules\Auth\CapitalfirstLogin;
use App\Rules\Auth\MixedCasePassword;
use App\Rules\Auth\NumbersPassword;
use App\Rules\Auth\WLogin;
use App\Services\Database\Person\Dto\RegisterDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['unique:App\Models\User', 'required', 'min:4', 'max:18', new CapitalfirstLogin, new WLogin],
            'email' => ['unique:App\Models\User', 'required', 'email'],
            'password' => ['required', 'confirmed', Password::min(6)->rules([new MixedCasePassword, new NumbersPassword])],
        ];
    }
    
    public function messages(): array
    {
        return [
            'login.unique' => trans("auth.unique.login"),
            'login.required' => trans("auth.required.login"),
            'login.max' => trans("auth.max.login"),
            'login.min' => trans("auth.min.login"),
            'email.unique' => trans("auth.unique.email"),
            'email.required' => trans("auth.required.email"),
            'email.email' => trans("auth.email"),
            'password.required' => trans("auth.required.password"),
            'password.confirmed' => trans("auth.confirmed.password"),
            'password.min' => trans("auth.min.password"),
        ];
    }
    
    public function getDto(): RegisterDto
    {
        return new RegisterDto($this->input('login'), $this->input('email'), $this->input('password'));
    }
}
