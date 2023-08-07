<?php

namespace App\Console\Commands\OpenWeather;

use App\Http\Controllers\Url;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetWeather extends Command
{
    use Url;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:weather {city_id?}';

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
        
        $city_id = $this->argument('city_id');
        
        if($city_id) {
            $cities = City::where('open_weather_id', $city_id)->get();
        } else {
            $cities = City::all();
        }
        
        foreach($cities as $city) {
            $this->line("$city->name [$city->open_weather_id]: отправляем запрос на сервер OpenWeather");

            $response = Http::get($this->getUrl("http://api.openweathermap.org/data/2.5/weather", [
                'units' => 'metric',
                'lang' => 'ru',
                'id' => $city->open_weather_id,
                'appid' => env('OPENWEATHER_KEY')
            ]));
            
            if($response->status() === 200) {
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
                $weather->save();
                
                $this->line("$city->name [$city->open_weather_id]: погода сохранена в базе");
            } else {
                $this->line("Сервер OpenWeather вернул ответ со статусом {$response->status()}: {$response->body()}");
            }
            
            $this->line('');
        }
        
        $this->info('Команда выполнена.');
    }
}