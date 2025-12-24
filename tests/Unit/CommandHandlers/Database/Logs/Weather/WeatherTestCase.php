<?php

namespace Tests\Unit\CommandHandlers\Database\Logs\Weather;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\DataTransferObjects\Database\OpenWeather\WeatherStatisticsDto;
use App\Models\Thesaurus\City;
use App\ValueObjects\ArrayItems\TimeInterval;
use App\ValueObjects\DateString;
use Tests\Unit\UnitTestCase;

abstract class WeatherTestCase extends UnitTestCase
{
    protected int $cityId = 18;
    
    protected function getWeatherFilterDto(): WeatherFilterDto
    {
        return new WeatherFilterDto(DateString::create('01.02.2025'), DateString::create('03.02.2025'));
    }
    
    protected function getWeatherStatisticsDto(): WeatherStatisticsDto
    {
        $city = new City();
        $city->id = $this->cityId;
        
        return new WeatherStatisticsDto(
                $this->getCity($this->cityId),
                DateString::create('01.01.2025'),
                DateString::create('11.02.2025'),
                TimeInterval::create('week'),
            );
    }
    
    protected function getCity(int $id): City
    {
        $city = new City();
        $city->id = $id;
        
        return $city;
    }
    
    protected function getWeatherAllStatistics(): object
    {
        return (object) [
                    'datefrom' => "01.12.2025",
                    'dateto' => "08.12.2025",
                    'avg' => "-6.390000000000001",
                    'max' => "-0.39",
                    'max_time' => "11:17:11 02.12.2025",
                    'min' => "-16.39",
                    'min_time' => "10:43:02 06.12.2025",
                ];
    }
    
    protected function getWeatherIntervalsStatistics(): array
    {
        return [ (object) [
                    'datefrom' => "01.12.2025",
                    'dateto' => "08.12.2025",
                    'avg' => "-6.390000000000001",
                    'max' => "-0.39",
                    'max_time' => "11:17:11 02.12.2025",
                    'min' => "-16.39",
                    'min_time' => "10:43:02 06.12.2025",
                ], (object) [
                    'datefrom' => "08.12.2025",
                    'dateto' => "15.12.2025",
                    'avg' => '',
                    'max' => '',
                    'max_time' => "10:39:48 13.12.2025",
                    'min' => '',
                    'min_time' => "09:38:49 09.12.2025",
                ], (object) [
                    'datefrom' => "15.12.2025",
                    'dateto' => "19.12.2025",
                    'avg' => "-10.39",
                    'max' => "-3.39",
                    'max_time' => "10:17:30 15.12.2025",
                    'min' => "-17.39",
                    'min_time' => "10:20:07 18.12.2025",
            ]];
    }
}
