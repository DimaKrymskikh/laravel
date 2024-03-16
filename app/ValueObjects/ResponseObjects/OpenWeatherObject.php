<?php

namespace App\ValueObjects\ResponseObjects;

readonly class OpenWeatherObject
{
    public object $data;
    
    private function __construct(object $data) {
        $this->data = (object) [
            'weatherDescription' => isset($data->weather[0]->description) ? $data->weather[0]->description : null,
            'mainTemp' => isset($data->main->temp) ? $data->main->temp : null,
            'mainFeelsLike' => isset($data->main->feels_like) ? $data->main->feels_like : null,
            'mainPressure' => isset($data->main->pressure) ? $data->main->pressure : null,
            'mainHumidity' => isset($data->main->humidity) ? $data->main->humidity : null,
            'visibility' => isset($data->visibility) ? $data->visibility : null,
            'windSpeed' => isset($data->wind->speed) ? $data->wind->speed : null,
            'windDeg' => isset($data->wind->deg) ? $data->wind->deg : null,
            'cloudsAll' => isset($data->clouds->all) ? $data->clouds->all : null,
        ];
    }
    
    public static function create(object $data): self
    {
        return new self($data);
    }
}
