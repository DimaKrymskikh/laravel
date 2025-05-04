<?php

namespace App\Queries\Thesaurus\Cities;

use App\Models\Thesaurus\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface CityQueriesInterface 
{
    public function exists(int $id): bool;
    
    public function getById(int $id): City;
    
    public function getByOpenWeatherId($openWeatherId): City|null;
    
    public function getList(): Collection;
    
    public function getByUserWithWeather(User $user): Collection;
    
    public function getListWithAvailableByUserId(int $userId): Collection;
}
