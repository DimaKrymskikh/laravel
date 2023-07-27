<?php

namespace App\Http\Requests\OpenWeather;

use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'open_weather_id' => ['required', 'unique:App\Models\Thesaurus\City', 'integer'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => trans("city.cityName.required"),
            'name.string' => trans("city.cityName.string"),
            'open_weather_id.required' => trans("city.openWeatherId.required"),
            'open_weather_id.unique' => trans("city.openWeatherId.unique"),
            'open_weather_id.integer' => trans("city.openWeatherId.integer"),
        ];
    }
}
