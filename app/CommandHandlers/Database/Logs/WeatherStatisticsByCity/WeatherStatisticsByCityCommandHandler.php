<?php

namespace App\CommandHandlers\Database\Logs\WeatherStatisticsByCity;

use App\Exceptions\DatabaseException;
use App\Exceptions\RuleException;
use App\Console\Commands\Logs\WeatherStatisticsByCity;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Services\Carbon\Enums\DateFormat;
use App\Services\Carbon\CarbonService;
use App\Services\Database\Thesaurus\TimezoneService;
use App\ValueObjects\ScalarTypes\SimpleStringValue;
use App\ValueObjects\IntValue;

final class WeatherStatisticsByCityCommandHandler
{
    public function __construct(
            private CityQueriesInterface $queries,
            private TimezoneService $timezoneService,
    ) {
    }
    
    /**
     * Выполнение логики команды 'statistics:weather {open_weather_id?}'
     * 
     * @param WeatherStatisticsByCity $command
     * @return void
     */
    public function handle(WeatherStatisticsByCity $command): void
    {
        $openWeatherId = $command->argument('open_weather_id');
        
        if($openWeatherId) {
            try {
                // Проверяем, что $openWeatherId - целое число.
                IntValue::create($openWeatherId, 'message', 'Параметр команды не является целым числом.');
                // Проверяем, что в таблице 'thesaurus.cities' имеется запись с open_weather_id = $openWeatherId.
                $this->queries->getByOpenWeatherId($openWeatherId);
            } catch(DatabaseException $ex) {
                $command->error($ex->getMessage());
                return;
            } catch(RuleException $ex) {
                $command->error($ex->getMessage());
                return;
            }
            
            $data = $this->queries->getObject(new SelectForOneCity(), ['open_weather_id' => $openWeatherId]);
            $this->renderStatisticsForCity($data, $command);
        } else {
            $data = $this->queries->getObject(new SelectForAllCities());
            $this->renderStatisticsForAllCities($data, $command);
        }
    }
    
    /**
     * Выводит в консоль статистику погоды для одного города.
     * 
     * @param object $data Данные, полученные из базы данных
     * @param WeatherStatisticsByCity $command
     * @return void
     */
    private function renderStatisticsForCity(object $data, WeatherStatisticsByCity $command): void
    {
        $city = $this->queries->getById($data->id);
        $tz = $this->timezoneService->getTimezoneByCity($city);
        
        $minTempDate = $this->processForCity(SimpleStringValue::create($data->min_temp_date), $tz);
        $maxTempDate = $this->processForCity(SimpleStringValue::create($data->max_temp_date), $tz);
        
        $command->line("Статистика по городу $data->name [$data->open_weather_id]");
        $command->newLine();
        $command->line("Минимальная температура: $data->min_temp");
        $command->line("Была достигнута $minTempDate");
        $command->newLine();
        $command->line("Максимальная температура: $data->max_temp");
        $command->line("Была достигнута $maxTempDate");
        $command->newLine();
        $command->line("Всего записей: $data->n");
    }
    
    /**
     * В статистике для одного города у времени устанавливает часовой пояс города.
     * 
     * @param SimpleStringValue $date
     * @param string $tz
     * @return string
     */
    private function processForCity(SimpleStringValue $date, string $tz): string
    {
        $arrTempDate = explode('|', $date->value);
        $newArrTempDate = array_map(fn($item) => CarbonService::setNewTimezoneInString($item, 'UTC', $tz, DateFormat::Full), $arrTempDate);
        
        return implode(', ', $newArrTempDate);
    }
    
    /**
     * Выводит в консоль статистику погоды для всех городов.
     * 
     * @param object $data Данные, полученные из базы данных
     * @param WeatherStatisticsByCity $command
     * @return void
     */
    private function renderStatisticsForAllCities(object $data, WeatherStatisticsByCity $command): void
    {
        $minDate = $this->processForAllCities($data->min_date);
        $maxDate = $this->processForAllCities($data->max_date);
        
        $command->line("Статистика по всем городам.");
        $command->newLine();
        $command->line("Минимальная температура: $data->min_temp");
        $command->line("Была достигнута $minDate");
        $command->newLine();
        $command->line("Максимальная температура: $data->max_temp");
        $command->line("Была достигнута $maxDate");
        $command->newLine();
    }
    
    /**
     * В статистике для всех городов у времени устанавливает часовой пояс соответствующего города.
     * 
     * @param string $strDate
     * @return string
     */
    private function processForAllCities(string $strDate): string
    {
        $arrDate = explode('|', $strDate);
        
        $newArrDate = [];
        foreach($arrDate as $item) {
            [$cityId, $strDate] = explode('_', $item);
            $city = $this->queries->getById($cityId);
            $tz = $this->timezoneService->getTimezoneByCity($city);
            $newDate = CarbonService::setNewTimezoneInString($strDate, 'UTC', $tz, DateFormat::Full);
            $newArrDate[] = "$city->name: $newDate";
        }
        
        return implode(', ', $newArrDate);
    }
}
