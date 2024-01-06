<?php

namespace App\Http\Requests\Dvd;

use Illuminate\Foundation\Http\FormRequest;

class FilmRequest extends FormRequest
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
            'title' => ['sometimes', 'required', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'release_year' => ['sometimes', 'nullable', 'integer'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'title.required' => trans("attr.film.title.required"),
            'title.string' => trans("attr.film.title.string"),
            'description.string' => trans("attr.film.description.string"),
            'release_year.integer' => trans("attr.film.release_year.integer"),
        ];
    }
}
