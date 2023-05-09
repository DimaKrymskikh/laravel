<?php

namespace App\Models;

use App\Models\Dvd\Film;

trait ModelsFields
{
    private function getModelField(string $model, string $field, int $id): string
    {
        return $model::select($field)->where('id', $id)->first()->$field;
    }
    
    public function getFilmTitle(int $film_id): string
    {
        return $this->getModelField(Film::class, 'title', $film_id);
    }
}
