<?php

namespace App\Modifiers\Thesaurus\Cities;

use App\Models\Thesaurus\City;

final class CityModifiers implements CityModifiersInterface
{
    public function save(City $city, string $name, int $openWeatherId): void
    {
        $city->name = $name;
        $city->open_weather_id = $openWeatherId;
        $city->save();
    }
    
    public function saveField(City $city, string $field, mixed $value): void
    {
        $city->$field = $value;
        $city->save();
    }
    
    public function delete(int $id): void
    {
        City::find($id)->delete();
    }
}
