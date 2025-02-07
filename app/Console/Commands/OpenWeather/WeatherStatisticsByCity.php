<?php

namespace App\Console\Commands\OpenWeather;

use App\Exceptions\DatabaseException;
use App\Exceptions\RuleException;
use App\Models\Thesaurus\City;
use App\Services\Database\Thesaurus\CityService;
use App\ValueObjects\IntValue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WeatherStatisticsByCity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:weather {open_weather_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Статистика погоды по городам';

    /**
     * Execute the console command.
     */
    public function handle(CityService $cityService): void
    {
        $this->info('Старт.');
        
        $open_weather_id = $this->argument('open_weather_id');
        
        if($open_weather_id) {
            try {
                $openWeatherId = IntValue::create($open_weather_id, 'message', 'Параметр команды не является целым числом.');
                $cityService->findCityByOpenWeatherId($openWeatherId->value);
            } catch(DatabaseException $ex) {
                $this->error($ex->getMessage());
                return;
            } catch(RuleException $ex) {
                $this->error($ex->getMessage());
                return;
            }
            
            $this->getStatisticsForCity($openWeatherId->value);
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
        
        $cities = City::leftJoin('logs.open_weather__weather', 'logs.open_weather__weather.city_id', '=', 'thesaurus.cities.id')
                    ->select(
                            'thesaurus.cities.name',
                            'thesaurus.cities.open_weather_id',
                            DB::raw('count(logs.open_weather__weather.city_id) as n')
                    )
                    ->groupBy('thesaurus.cities.id')
                    ->orderBy('thesaurus.cities.name')
                    ->get();
        
        foreach ($cities as $city) {
            $this->line("$city->name [$city->open_weather_id] содержит $city->n записей");
        }
    }
    
    private function getStatisticsForCity(int $open_weather_id): void
    {
        $city = City::leftJoin('logs.open_weather__weather', 'logs.open_weather__weather.city_id', '=', 'thesaurus.cities.id')
                ->select(
                        'thesaurus.cities.name',
                        'thesaurus.cities.open_weather_id',
                        DB::raw('count(logs.open_weather__weather.city_id) AS n'),
                        DB::raw("coalesce(MIN(logs.open_weather__weather.main_temp)::text, 'отсутствует') AS min_temp"),
                        DB::raw("coalesce(MAX(logs.open_weather__weather.main_temp)::text, 'отсутствует') AS max_temp"),
                )
                ->where('thesaurus.cities.open_weather_id', $open_weather_id)
                ->groupBy('thesaurus.cities.id')
                ->first();
        
        $this->line("Статистика по городу $city->name [$city->open_weather_id]");
        $this->newLine();
        $this->line("Максимальная температура: $city->max_temp");
        $this->line("Минимальная температура: $city->min_temp");
        $this->line("Всего записей: $city->n");
    }
}
