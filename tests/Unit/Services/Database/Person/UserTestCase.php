<?php

namespace Tests\Unit\Services\Database\Person;

use App\Models\Dvd\Film;
use App\Models\Thesaurus\City;
use App\Services\Database\Person\Dto\UserCityDto;
use App\Services\Database\Person\Dto\UserFilmDto;
use PHPUnit\Framework\TestCase;

abstract class UserTestCase extends TestCase
{
    protected function getUserCityDto(): UserCityDto
    {
        $userId = 8;
        $cityId = 29;
        return new UserCityDto($userId, $cityId);
    }
    
    protected function getUserFilmDto(): UserFilmDto
    {
        $userId = 8;
        $filmId = 17;
        return new UserFilmDto($userId, $filmId);
    }
    
    protected function factoryCity(): City
    {
        return City::factory()
                ->state([
                    'name' => 'TestCityName',
                ])
                ->make();
    }
    
    protected function factoryFilm(): Film
    {
        return Film::factory()
                ->state([
                    'title' => 'TestFilmTitle',
                ])
                ->make();
    }
}
