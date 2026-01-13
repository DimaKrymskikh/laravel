<?php

namespace App\CommandHandlers\Database\Logs\WeatherStatisticsByCity;

/**
 * Класс содержит строку запроса, которая получает статистику для всех городов.
 */
final class SelectForAllCities
{
    public function __toString(): string
    {
        return <<<SQL
                WITH _ AS (
                    SELECT
                        MIN(main_temp) AS min_temp,
                        MAX(main_temp) AS max_temp
                    FROM logs.open_weather__weather
                ), _min AS (
                    SELECT
                        string_agg(concat(city_id, '_', to_char(created_at, 'HH24:MI:SS DD.MM.YYYY')), '|') AS min_date
                    FROM logs.open_weather__weather, _
                    WHERE main_temp = _.min_temp
                ), _max AS (
                    SELECT
                        string_agg(concat(city_id, '_', to_char(created_at, 'HH24:MI:SS DD.MM.YYYY')), '|') AS max_date
                    FROM logs.open_weather__weather, _
                    WHERE main_temp = _.max_temp
                )
                SELECT 
                    _.min_temp,
                    _.max_temp,
                    min_date, 
                    max_date
                FROM _, _min, _max
            SQL;
    }
}
