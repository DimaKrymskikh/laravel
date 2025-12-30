<?php

namespace App\Console\Commands\database\OpenWeather;

use App\Services\StorageDisk\OpenWeather\CopyWeatherService;
use Illuminate\Console\Command;

class CopyWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:open_weather.weather';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Копирование данных таблицы open_weather.weather в файл database/Copy/OpenWeather/WeatherData.php';

    /**
     * Execute the console command.
     */
    public function handle(CopyWeatherService $service): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $service->copy();
        
        $this->info('Команда выполнена.');
    }
}
