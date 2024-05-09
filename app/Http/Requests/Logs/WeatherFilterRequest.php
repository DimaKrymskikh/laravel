<?php

namespace App\Http\Requests\Logs;

use Illuminate\Foundation\Http\FormRequest;

class WeatherFilterRequest extends FormRequest
{
    protected $redirect = 'userweather';
    
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
            'page' => ['integer'],
            'number' => ['integer'],
            'datefrom' => ['nullable', 'date'],
            'dateto' => ['nullable', 'date'],
        ];
    }
}
