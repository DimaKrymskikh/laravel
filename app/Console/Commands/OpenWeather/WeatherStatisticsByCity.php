<?php

namespace App\Console\Commands\OpenWeather;

use App\Models\Thesaurus\City;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WeatherStatisticsByCity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:weather {city_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Статистика погоды по городам';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Старт.');
        
        $city_id = $this->argument('city_id');
        
        if($city_id) {
            $this->getStatisticsForCity($city_id);
        } else {
            $this->getStatisticsForAllCities();
        }
        
        $this->line('');
        $this->info('Команда выполнена.');
    }
    
    private function getStatisticsForAllCities(): void
    {
        $this->line("$this->description");
        $this->line('');
        
        $cities = City::leftJoin('open_weather.weather', 'open_weather.weather.city_id', '=', 'thesaurus.cities.id')
                    ->select(
                            'thesaurus.cities.name',
                            'thesaurus.cities.open_weather_id',
                            DB::raw('count(open_weather.weather.city_id) as n')
                    )
                    ->groupBy('thesaurus.cities.id')
                    ->orderBy('thesaurus.cities.name')
                    ->get();
        
        foreach ($cities as $city) {
            $this->line("$city->name [$city->open_weather_id] содержит $city->n записей");
        }
    }
    
    private function getStatisticsForCity(int $city_id): void
    {
        $city = City::leftJoin('open_weather.weather', 'open_weather.weather.city_id', '=', 'thesaurus.cities.id')
                ->select(
                        'thesaurus.cities.name',
                        'thesaurus.cities.open_weather_id',
                        DB::raw('count(open_weather.weather.city_id) AS n'),
                        DB::raw("coalesce(MIN(open_weather.weather.main_temp)::text, 'отсутствует') AS min_temp"),
                        DB::raw("coalesce(MAX(open_weather.weather.main_temp)::text, 'отсутствует') AS max_temp"),
                )
                ->where('thesaurus.cities.open_weather_id', $city_id)
                ->groupBy('thesaurus.cities.id')
                ->first();
        
        $this->line("Статистика по городу $city->name [$city->open_weather_id]");
        $this->line('');
        $this->line("Максимальная температура: $city->max_temp");
        $this->line("Минимальная температура: $city->min_temp");
        $this->line("Всего записей: $city->n");
    }
}
