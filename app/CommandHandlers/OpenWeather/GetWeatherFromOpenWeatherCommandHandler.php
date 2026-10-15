<?php

namespace App\CommandHandlers\OpenWeather;

use App\Contracts\Services\Database\Thesaurus\InfoAboutCitiesInterface;
use App\Contracts\Services\OpenWeather\OpenWeatherInterface;
use App\Exceptions\DatabaseException;
use App\Exceptions\OpenWeatherException;
use App\Exceptions\RuleException;
use App\Console\Commands\OpenWeather\GetWeather;
use App\ValueObjects\IntValue;

/**
 * Бизнес-логика команды 'get:weather {open_weather_id?}'
 */
final class GetWeatherFromOpenWeatherCommandHandler
{
    public function __construct(
            private OpenWeatherInterface $weatherService,
            private InfoAboutCitiesInterface $cityService,
    ) {
    }
    
    /**
     * Выполнение логики команды 'get:weather {open_weather_id?}'
     * 
     * @param GetWeather $command
     * @return void
     */
    public function handle(GetWeather $command): void
    {
        
        $open_weather_id = $command->argument('open_weather_id');
        
        if($open_weather_id) {
            try {
                $openWeatherId = IntValue::create($open_weather_id, 'message', 'Параметр команды не является целым числом.');
                $city = $this->cityService->getCityByOpenWeatherId($openWeatherId->value);
            } catch(DatabaseException $ex) {
                $command->error($ex->getMessage());
                return;
            } catch(RuleException $ex) {
                $command->error($ex->getMessage());
                return;
            }
            
            $cities = collect([$city]);
        } else {
            $cities = $this->cityService->getAllCitiesList();
        }
        
        foreach($cities as $city) {
            $command->line("$city->name [$city->open_weather_id]: отправляем запрос на сервер OpenWeather");

            try {
                $response = $this->weatherService->sendRequest($city);
            } catch(OpenWeatherException $ex) {
                $ex->report();
                $command->error($ex->getMessage());
                $command->info("Выполнение команды прервано.");
                return;
            }
            
            if($response->status() === 200) {
                $this->weatherService->saveResponse($response, $city);
                $command->line("$city->name [$city->open_weather_id]: погода сохранена в базе");
            } else {
                $command->line("Сервер OpenWeather вернул ответ со статусом {$response->status()}: {$response->body()}");
            }
            
            $command->newLine();
        }
    }
}
