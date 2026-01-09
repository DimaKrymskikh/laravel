<?php

namespace App\Console\Commands\OpenWeather;

use App\CommandHandlers\OpenWeather\GetWeatherFromOpenWeatherCommandHandler;
use Illuminate\Console\Command;

class GetWeather extends Command
{
    /**
     * Для всех городов: get:weather.
     * Для одного города: get:weather open_weather_id.
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
     * @param GetWeatherFromOpenWeatherCommandHandler $handler
     * @return void
     */
    public function handle(GetWeatherFromOpenWeatherCommandHandler $handler): void
    {
        $this->info('Старт.');
        $this->line("$this->description");
        $this->line('');
        
        $handler->handle($this);
        
        $this->info('Команда выполнена.');
    }
}
