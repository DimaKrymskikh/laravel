<?php

namespace App\Repositories\Thesaurus;

use App\Models\Thesaurus\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface CityRepositoryInterface
{
    public function save(City $city, string $name, int $openWeatherId): void;
    
    public function saveField(City $city, string $field, mixed $value): void;
    
    public function delete(int $id): void;
    
    public function getById(int $id): City;
    
    public function getByOpenWeatherId($openWeatherId): ?City;
        
    public function getList(): Collection;
    
    public function getByUserWithWeather(User $user): Collection;
    
    public function getListWithAvailableByUserId(int $userId): Collection;
}
