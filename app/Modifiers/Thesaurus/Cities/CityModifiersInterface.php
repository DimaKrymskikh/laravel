<?php

namespace App\Modifiers\Thesaurus\Cities;

use App\Models\Thesaurus\City;

interface CityModifiersInterface
{
    public function save(City $city, string $name, int $openWeatherId): void;
    
    public function saveField(City $city, string $field, mixed $value): void;
    
    public function delete(int $id): void;
}
