<?php

namespace App\Console\Commands\OpenWeather;

use App\CommandHandlers\OpenWeather\GetWeatherFromOpenWeatherCommandHandler;
use App\ValueObjects\Date\TimeStringFromSeconds;
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
        $start = microtime(true);
        $this->line($this->description);
        $this->line('');
        
        $handler->handle($this);
        
        $this->line('Время выполнения: '.TimeStringFromSeconds::create(microtime(true) - $start)->value);
        $this->info('Команда выполнена.');
    }
}
