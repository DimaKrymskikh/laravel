<?php

namespace App\Console\Commands\OpenWeather;

use App\Events\RefreshCityWeather;
use App\Support\Url\Url;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GetWeather extends Command
{
    use Url;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:weather {open_weather_id?} {--http} {--user_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получение погоды с сервиса OpenWeather';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Старт.');
        $this->line("$this->description");
        $this->line('');
        
        $open_weather_id = $this->argument('open_weather_id');
        $isHttp = $this->option('http');
        $userId = $this->option('user_id');
        
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

            $response = Http::get($this->getUrl("http://api.openweathermap.org/data/2.5/weather", [
                'units' => 'metric',
                'lang' => 'ru',
                'id' => $city->open_weather_id,
                'appid' => config('api.openweather_key')
            ]));
            
            if($response->status() === 200) {
                $this->responseStatusOk($response, $city, $isHttp, $userId);
            } else {
                $this->line("Сервер OpenWeather вернул ответ со статусом {$response->status()}: {$response->body()}");
            }
            
            $this->newLine();
        }
        
        $this->info('Команда выполнена.');
    }
    
    private function responseStatusOk(Response $response, City $city, bool $isHttp, ?int $userId): void
    {
        $data = $response->object();

        $weather = new Weather();
        $weather->city_id = $city->id;
        $weather->weather_description = $data->weather[0]->description;
        $weather->main_temp = $data->main->temp;
        $weather->main_feels_like = $data->main->feels_like;
        $weather->main_pressure = $data->main->pressure;
        $weather->main_humidity = $data->main->humidity;
        $weather->visibility = $data->visibility;
        $weather->wind_speed = $data->wind->speed;
        $weather->wind_deg = $data->wind->deg;
        $weather->clouds_all = $data->clouds->all;
        // Задаём время здесь, чтобы не делать запрос в RefreshCityWeather
        // Время нужно задавать с часовым поясом 'UTC'
        $weather->created_at = Carbon::now('UTC');

        if($weather->save()) {
            $this->line("$city->name [$city->open_weather_id]: погода сохранена в базе");
            if($isHttp && $userId) {
                event(new RefreshCityWeather($weather, $city->id, $userId));
            }
        }
    }
}
