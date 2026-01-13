<?php

namespace App\Console\Commands\Logs;

use App\CommandHandlers\Database\Logs\WeatherStatisticsByCity\WeatherStatisticsByCityCommandHandler;
use Illuminate\Console\Command;

class WeatherStatisticsByCity extends Command
{
    /**
     * Для всех городов: statistics:weather.
     * Для одного города: statistics:weather open_weather_id.
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
     * Выполняет консольную команду.
     * 
     * @param WeatherStatisticsByCityCommandHandler $handler
     * @return void
     */
    public function handle(WeatherStatisticsByCityCommandHandler $handler): void
    {
        $this->info('Старт.');
        
        $handler->handle($this);
        
        $this->info('Команда выполнена.');
    }
}
