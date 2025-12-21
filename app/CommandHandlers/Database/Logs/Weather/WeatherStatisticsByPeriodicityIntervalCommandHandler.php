<?php

namespace App\CommandHandlers\Database\Logs\Weather;

use App\DataTransferObjects\Database\OpenWeather\WeatherStatisticsDto;
use App\Models\Thesaurus\City;
use App\Queries\Logs\OpenWeatherWeather\OpenWeatherWeatherQueriesInterface;
use App\Services\CarbonService;
use App\Services\Database\Thesaurus\TimezoneService;
use Carbon\Carbon;

/**
 * Класс для получения статистики погоды
 */
final class WeatherStatisticsByPeriodicityIntervalCommandHandler
{
    public function __construct(
            private OpenWeatherWeatherQueriesInterface $queries,
            private TimezoneService $timezoneService
    ) {
    }
    
    /**
     * Получает статистику погоды в выбранном городе за заданный промежуток времени с разбивкой по временным интервалам.
     * 
     * @param WeatherStatisticsDto $dto
     * @return array
     */
    public function handle(WeatherStatisticsDto $dto): array
    {
        $fn = 'add'.$dto->interval->value.'s';
        $query = '';
        
        $n = 0;
        while(Carbon::parse($dto->datefrom->value)->$fn($n)->timestamp < Carbon::parse($dto->dateto->value)->timestamp) {
            $n++;
            $query .= $this->withBlock($n, $dto);
        }
        
        for($m = 1; $m <= $n; $m++) {
            $query .= $this->selectBlock($m, $dto);
        }
        
        $weather = $this->queries->getArray($query);
        
        $this->setFieldsFormat($dto->city, $weather);
        
        return $weather;
    }
    
    private function withBlock(int $nInterval, WeatherStatisticsDto $dto): string
    {
        $minusOne = $nInterval - 1;
        $query = $minusOne ? ',' : 'WITH';
        
        $query .= " _$nInterval AS (";
        $query .= "SELECT created_at, main_temp FROM logs.open_weather__weather WHERE city_id = {$dto->city->id} ";
        $query .= "AND created_at >= to_date('{$dto->datefrom->value}', 'DD.MM.YYYY')";
        $query .= $minusOne ? " + '$minusOne {$dto->interval->value}'::interval " : ' ';
        $query .= "AND created_at < least(";
        $query .= "to_date('{$dto->datefrom->value}', 'DD.MM.YYYY') + '$nInterval {$dto->interval->value}'::interval, ";
        $query .= "to_date('{$dto->dateto->value}', 'DD.MM.YYYY')";
        $query .= ')'; // Закрывает least(
        $query .= ')'; // Закрывает as (
        
        return $query;
    }
    
    private function selectBlock(int $nInterval, WeatherStatisticsDto $dto): string
    {
        $minusOne = $nInterval - 1;
        $query = $minusOne ? ' UNION ALL ' : ' ';
        
        $query .= 'SELECT ';
        $query .= "to_char(to_date('{$dto->datefrom->value}', 'DD.MM.YYYY') ";
        $query .= $minusOne ? " + '$minusOne {$dto->interval->value}'::interval " : ' ';
        $query .= ", 'DD.MM.YYYY') AS datefrom, ";
        
        $query .= "to_char(least(";
        $query .= "to_date('{$dto->datefrom->value}', 'DD.MM.YYYY') + '$nInterval {$dto->interval->value}'::interval, ";
        $query .= "to_date('{$dto->dateto->value}', 'DD.MM.YYYY')";
        $query .= "), 'DD.MM.YYYY') AS dateto, ";
        
        $query .= "avg(main_temp) AS avg, ";
        
        $query .= "max(main_temp) AS max, ";
        
        $query .= "(SELECT string_agg(to_char(created_at, 'HH24:MI:SS DD.MM.YYYY'), ', ') FROM _$nInterval ";
        $query .= "WHERE main_temp = (SELECT max(main_temp) FROM _$nInterval)) AS max_time, ";
        
        $query .= "min(main_temp) AS min, ";
        
        $query .= "(SELECT string_agg(to_char(created_at, 'HH24:MI:SS DD.MM.YYYY'), ', ') FROM _$nInterval ";
        $query .= "WHERE main_temp = (SELECT min(main_temp) FROM _$nInterval)) AS min_time ";
        $query .= "FROM _$nInterval ";

        return $query;
    }
    
    private function setFieldsFormat(City $city, array $weather): void
    {
        $tzName = $this->timezoneService->getTimezoneByCity($city);
        
        foreach($weather as $item) {
            $item->datefrom = CarbonService::setNewTimezoneInString($item->datefrom, 'UTC', $tzName, 'd.m.Y');
            $item->dateto = CarbonService::setNewTimezoneInString($item->dateto, 'UTC', $tzName, 'd.m.Y');
            $item->avg = sprintf("%.2f", $item->avg);
            $item->max_time = $this->changeTimezoneInDateLine($item->max_time ??  '', $tzName);
            $item->min_time = $this->changeTimezoneInDateLine($item->min_time ??  '', $tzName);
        }
    }
    
    /**
     * В строке дат, разделённых запятой, изменяет часовой пояс с 'UTC' на часовой пояс города.
     * Если $date - пустая строка, то метод вернёт пустую строку.
     * 
     * @param string $date Строка дат, разделённых запятой.
     * @param string $tzName Часовой пояс города.
     * @return string Строка дат с часовым поясом города.
     */
    private function changeTimezoneInDateLine(string $date, string $tzName): string
    {
            $arrDate = explode(',', $date);
            $newArrDate = array_map(fn($time) => CarbonService::setNewTimezoneInString($time, 'UTC', $tzName, 'H:i:s d.m.Y'), $arrDate);
            return implode(', ', $newArrDate);
    }
}
