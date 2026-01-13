<?php

namespace App\CommandHandlers\Database\Logs\WeatherStatisticsByCity;

/**
 * Класс содержит строку запроса, которая получает статистику для одного города.
 */
final class SelectForOneCity
{
    public function __toString(): string
    {
        return <<<SQL
                WITH _ AS (
                    SELECT
                        c.id,
                        c.name,
                        c.open_weather_id,
                        count(oww.city_id) AS n,
                        coalesce(MIN(oww.main_temp)::text, 'отсутствует') AS min_temp,
                        coalesce(MAX(oww.main_temp)::text, 'отсутствует') AS max_temp
                    FROM thesaurus.cities c 
                    LEFT JOIN logs.open_weather__weather oww ON c.id = oww.city_id 
                    WHERE c.open_weather_id = :open_weather_id
                    GROUP BY c.id
                )
                SELECT
                    _.id,
                    _.name,
                    _.open_weather_id,
                    _.n,
                    _.min_temp,
                    (
                        SELECT string_agg(to_char(created_at, 'HH24:MI:SS DD.MM.YYYY'), '|')
                        FROM logs.open_weather__weather
                        WHERE city_id = _.id
                            AND main_temp::text = _.min_temp
                    ) AS min_temp_date,
                    _.max_temp,
                    (
                        SELECT string_agg(to_char(created_at, 'HH24:MI:SS DD.MM.YYYY'), '|')
                        FROM logs.open_weather__weather
                        WHERE city_id = _.id
                            AND main_temp::text = _.max_temp
                    ) AS max_temp_date
                FROM _
            SQL;
    }
}
