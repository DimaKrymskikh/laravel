<?php

namespace App\Http\Requests\Logs\Filters;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\Http\Requests\PaginatorRequest;
use App\ValueObjects\DateString;

class WeatherFilterRequest extends PaginatorRequest
{
    protected $redirect = 'userweather';
    
    public function getWeatherFilterDto(): WeatherFilterDto
    {
        return new WeatherFilterDto(
                DateString::create($this->input('datefrom')),
                DateString::create($this->input('dateto')),
            );
    }
}
