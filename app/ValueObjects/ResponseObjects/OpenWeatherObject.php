<?php

namespace App\ValueObjects\ResponseObjects;

final readonly class OpenWeatherObject
{
    public object $data;
    
    private function __construct(object $data) {
        $this->data = (object) [
            'weatherDescription' => $data->weather[0]->description?? null,
            'mainTemp' => $data->main->temp ?? null,
            'mainFeelsLike' => $data->main->feels_like ?? null,
            'mainPressure' => $data->main->pressure ?? null,
            'mainHumidity' => $data->main->humidity ?? null,
            'visibility' => $data->visibility ?? null,
            'windSpeed' => $data->wind->speed ?? null,
            'windDeg' => $data->wind->deg ?? null,
            'cloudsAll' => $data->clouds->all ?? null,
        ];
    }
    
    public static function create(object $data): self
    {
        return new self($data);
    }
}
