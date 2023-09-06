<?php

namespace App\Console\Commands\database\Thesaurus;

use App\Models\Thesaurus\City;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CopyCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:thesaurus.cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Копирование данных таблицы thesaurus.cities в файл database/Copy/Thesaurus/CityData.php';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $file = 'Thesaurus/CityData.php';
        
        Storage::disk('database')->put($file, "<?php\n");
        
        Storage::disk('database')->append($file, "namespace Database\Copy\Thesaurus;\n");
        
        Storage::disk('database')->append($file, "// Данные таблицы thesaurus.cities");
        Storage::disk('database')->append($file, "class CityData");
        Storage::disk('database')->append($file, "{");
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "public function __invoke(): array");
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "{");
        Storage::disk('database')->append($file, Str::repeat(' ', 8) . "return [");
        
        foreach ($this->getCities() as $city) {
            Storage::disk('database')->append($file, Str::repeat(' ', 12) . "(object) [");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'id' => $city->id,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'name' => '$city->name',");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'open_weather_id' => $city->open_weather_id,");
            Storage::disk('database')->append($file, Str::repeat(' ', 12) . "],");
        }
        
        Storage::disk('database')->append($file, Str::repeat(' ', 8) . "];");
        
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "}");
        Storage::disk('database')->append($file, "}\n");
        
        $this->info('Команда выполнена.');
    }
    
    private function getCities(): iterable
    {
        yield from City::select('id', 'name', 'open_weather_id')->orderBy('id')->get();
    }
}
