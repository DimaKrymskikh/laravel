<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use App\Rules\Auth\CapitalfirstLogin;
use App\Rules\Auth\MixedCasePassword;
use App\Rules\Auth\NumbersPassword;
use App\Rules\Auth\WLogin;

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
            'password.required' => trans("auth.required.password"),
            'password.confirmed' => trans("auth.confirmed.password"),
            'password.min' => trans("auth.min.password"),
        ];
    }
}
