<?php

namespace App\Console\Commands\OpenWeather;

use App\Exceptions\OpenWeatherException;
use App\CommandHandlers\OpenWeather\GetWeatherFromOpenWeatherCommandHandler;
use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\Thesaurus\City;
use App\Repositories\OpenWeather\WeatherRepository;
use App\Services\Database\OpenWeather\WeatherService;
use App\Services\Database\Thesaurus\CityService;
use App\ValueObjects\IntValue;
use App\ValueObjects\ResponseObjects\OpenWeatherObject;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Validation\ValidationException;

class GetWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:weather {open_weather_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получение погоды с сервиса OpenWeather';

    /**
     * Выполняет консольную команду.
     * 
     * @param WeatherService $weatherService
     * @param GetWeatherFromOpenWeatherCommandHandler $request
     * @return void
     */
    public function handle(WeatherService $weatherService, CityService $cityService, GetWeatherFromOpenWeatherCommandHandler $request): void
    {
        $this->info('Старт.');
        $this->line("$this->description");
        $this->line('');
        
        $open_weather_id = $this->argument('open_weather_id');
        
        if($open_weather_id) {
            try {
                $openWeatherId = IntValue::create($open_weather_id, 'message', 'commands.parameter.int');
                $city = $cityService->findCityByOpenWeatherId($openWeatherId->value);
            } catch(ValidationException $ex) {
                $this->error($ex->getMessage());
                return;
            }
            
            $cities = collect([$city]);
        } else {
            $cities = City::all();
        }
        
        foreach($cities as $city) {
            $this->line("$city->name [$city->open_weather_id]: отправляем запрос на сервер OpenWeather");

            try {
                $response = $request->handle($city, new WeatherRepository());
            } catch(OpenWeatherException $ex) {
                report($ex);
                $this->error($ex->getMessage());
                $this->info("Выполнение команды прервано.");
                return;
            }
            
            if($response->status() === 200) {
                $this->responseStatusOk($response, $city, $weatherService);
            } else {
                $this->line("Сервер OpenWeather вернул ответ со статусом {$response->status()}: {$response->body()}");
            }
            
            $this->newLine();
        }
        
        $this->info('Команда выполнена.');
    }
    
    /**
     * Сохраняет данные о погоде в таблице open_weather.weather
     * 
     * @param Response $response
     * @param City $city
     * @param WeatherService $weatherService
     * @return void
     */
    private function responseStatusOk(Response $response, City $city, WeatherService $weatherService): void
    {
        $dto = new WeatherDto($city->id, OpenWeatherObject::create($response->object()));
        
        $weatherService->updateOrCreate($dto);
        $this->line("$city->name [$city->open_weather_id]: погода сохранена в базе");
    }
}
