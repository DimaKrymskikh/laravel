<?php

namespace App\DataTransferObjects\Database\OpenWeather;

use App\Models\Thesaurus\City;
use App\ValueObjects\ArrayItems\TimeInterval;
use App\ValueObjects\Date\DateString;

/**
 * Содержит данные для получения статистики погоды.
 */
final readonly class WeatherStatisticsDto
{
    /**
     * Сохраняет данные для получения статистики погоды.
     * 
     * @param City $city Город, для которого нужно получить статистику погоды.
     * @param DateString $datefrom Левая граница промежутка времени, для которого берётся статистика погоды.
     * @param DateString $dateto Правая граница промежутка времени, для которого берётся статистика погоды.
     * @param TimeInterval $interval Интервал периодичности, на который разбивается промежуток времени.
     */
    public function __construct(
            public City  $city,
            public DateString $datefrom,
            public DateString $dateto,
            public TimeInterval $interval,
    ) {
    }
}
