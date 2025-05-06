<?php

namespace App\Queries\Thesaurus\Cities;

use App\Models\Thesaurus\City;
use App\Models\User;
use App\Queries\SimpleQueriesInterface;
use Illuminate\Database\Eloquent\Collection;

interface CityQueriesInterface extends SimpleQueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'thesaurus.cities' нет записи с id=%d";
    const NOT_RECORD_WITH_OPEN_WEATHER_ID = "В таблице 'thesaurus.cities' нет записи с open_weather_id=%d";
    
    public function getByOpenWeatherId($openWeatherId): City;
    
    public function getList(): Collection;
    
    public function getByUserWithWeather(User $user): Collection;
    
    public function getListWithAvailableByUserId(int $userId): Collection;
}
