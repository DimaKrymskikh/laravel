<?php

namespace App\CommandHandlers\OpenWeather;

use App\Exceptions\DatabaseException;
use App\Exceptions\OpenWeatherException;
use App\Exceptions\RuleException;
use App\CommandHandlers\OpenWeather\Facades\OpenWeatherFacadeInterface;
use App\Console\Commands\OpenWeather\GetWeather;
use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\Thesaurus\City;
use App\ValueObjects\IntValue;
use App\ValueObjects\ResponseObjects\OpenWeatherObject;
use Illuminate\Http\Client\Response;

final class GetWeatherFromOpenWeatherCommandHandler
{
    public function __construct(
            private OpenWeatherFacadeInterface $facade,
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
                $city = $this->facade->findCityByOpenWeatherId($openWeatherId->value);
            } catch(DatabaseException $ex) {
                $command->error($ex->getMessage());
                return;
            } catch(RuleException $ex) {
                $command->error($ex->getMessage());
                return;
            }
            
            $cities = collect([$city]);
        } else {
            $cities = $this->facade->getAllCitiesList();
        }
        
        foreach($cities as $city) {
            $command->line("$city->name [$city->open_weather_id]: отправляем запрос на сервер OpenWeather");

            try {
                $response = $this->sendRequest($city);
            } catch(OpenWeatherException $ex) {
                $ex->report();
                $command->error($ex->getMessage());
                $command->info("Выполнение команды прервано.");
                return;
            }
            
            if($response->status() === 200) {
                $this->updateOrCreate($response, $city);
                $command->line("$city->name [$city->open_weather_id]: погода сохранена в базе");
            } else {
                $command->line("Сервер OpenWeather вернул ответ со статусом {$response->status()}: {$response->body()}");
            }
            
            $command->newLine();
        }
    }
    
    /**
     * Отправляет http-запрос на сервер OpenWeather
     * 
     * @param City $city
     * @return Response
     */
    public function sendRequest(City $city): Response
    {
        $this->facade->checkNumberOfWeatherLinesForLastMinuteLessBaseValue();
        $this->facade->checkTooEarlyToSubmitRequestForThisCity($city->id);
        return $this->facade->getWeatherByCity($city);
    }
    
    /**
     * Сохраняет данные ответа в таблице open_weather.weather
     * 
     * @param Response $response
     * @param City $city
     * @return void
     */
    public function updateOrCreate(Response $response, City $city): void
    {
        $dto = new WeatherDto($city->id, OpenWeatherObject::create($response->object()));
        $this->facade->updateOrCreate($dto);
    }
}
