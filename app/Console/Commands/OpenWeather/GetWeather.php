<?php

namespace App\Console\Commands\OpenWeather;

use App\CommandHandlers\OpenWeather\GetWeatherFromOpenWeatherCommandHandler;
use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\Thesaurus\City;
use App\Services\Database\OpenWeather\WeatherService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;

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
    public function handle(WeatherService $weatherService, GetWeatherFromOpenWeatherCommandHandler $request): void
    {
        $this->info('Старт.');
        $this->line("$this->description");
        $this->line('');
        
        $open_weather_id = $this->argument('open_weather_id');
        
        if($open_weather_id) {
            if(!intval($open_weather_id)) {
                $this->error("Параметр команды не является целым числом.");
                $this->info("Выполнение команды прервано.");
                return;
            }
            $cities = City::where('open_weather_id', $open_weather_id)->get();
            if(!$cities->count()) {
                $this->error("В таблице 'thesaurus.cities' нет городов с полем open_weather_id = $open_weather_id.");
                $this->info("Выполнение команды прервано.");
                return;
            }
        } else {
            $cities = City::all();
        }
        
        foreach($cities as $city) {
            $this->line("$city->name [$city->open_weather_id]: отправляем запрос на сервер OpenWeather");

            $response = $request->handle($city->open_weather_id);
            
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
        $data = $response->object();
        
        $dto = new WeatherDto(
                $city->id, $data->weather[0]->description, $data->main->temp, $data->main->feels_like, $data->main->pressure,
                $data->main->humidity, $data->visibility, $data->wind->speed, $data->wind->deg, $data->clouds->all
        );
        
        $weatherService->create($dto);
        $this->line("$city->name [$city->open_weather_id]: погода сохранена в базе");
    }
}
