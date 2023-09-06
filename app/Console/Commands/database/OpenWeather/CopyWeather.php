<?php

namespace App\Console\Commands\database\OpenWeather;

use App\Models\OpenWeather\Weather;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    public function handle(): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $file = 'OpenWeather/WeatherData.php';
        
        Storage::disk('database')->put($file, "<?php\n");
        
        Storage::disk('database')->append($file, "namespace Database\Copy\OpenWeather;\n");
        
        Storage::disk('database')->append($file, "// Данные таблицы open_weather.weather");
        Storage::disk('database')->append($file, "class WeatherData");
        Storage::disk('database')->append($file, "{");
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "public function __invoke(): array");
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "{");
        Storage::disk('database')->append($file, Str::repeat(' ', 8) . "return [");
        
        foreach ($this->getWeather() as $weather) {
            Storage::disk('database')->append($file, Str::repeat(' ', 12) . "(object) [");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'id' => $weather->id,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'city_id' => $weather->city_id,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'weather_description' => '$weather->weather_description',");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'main_temp' => $weather->main_temp,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'main_feels_like' => $weather->main_feels_like,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'main_pressure' => $weather->main_pressure,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'main_humidity' => $weather->main_humidity,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'visibility' => $weather->visibility,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'wind_speed' => $weather->wind_speed,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'wind_deg' => $weather->wind_deg,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'clouds_all' => $weather->clouds_all,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'created_at' => '$weather->created_at',");
            Storage::disk('database')->append($file, Str::repeat(' ', 12) . "],");
        }
        
        Storage::disk('database')->append($file, Str::repeat(' ', 8) . "];");
        
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "}");
        Storage::disk('database')->append($file, "}\n");
        
        $this->info('Команда выполнена.');
    }
    
    private function getWeather(): iterable
    {
        yield from Weather::select(
                'id',
                'city_id', 
                'weather_description', 
                'main_temp',
                'main_feels_like', 
                'main_pressure', 
                'main_humidity', 
                'visibility', 
                'wind_speed', 
                'wind_deg',
                'clouds_all',
                'created_at'
            )->orderBy('id')->get();
    }
}
