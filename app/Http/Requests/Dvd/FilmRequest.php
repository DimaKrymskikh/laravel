<?php

namespace App\Http\Requests\Dvd;

use App\DataTransferObjects\Database\Dvd\FilmDto;
use App\Http\Requests\Dvd\Filters\FilmFilterRequest;
use App\ValueObjects\IntValue;

class FilmRequest extends FilmFilterRequest
{
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
    
    public function getFilmDto(): FilmDto
    {
        return new FilmDto(
                $this->input('title'),
                $this->input('description'),
                IntValue::create($this->input('release_year')),
            );
    }
}
