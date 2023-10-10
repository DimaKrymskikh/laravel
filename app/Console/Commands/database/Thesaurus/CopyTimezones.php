<?php

namespace App\Console\Commands\database\Thesaurus;

use App\Models\Thesaurus\Timezone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CopyTimezones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:thesaurus.timezones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Копирование данных таблицы thesaurus.timezones в файл database/Copy/Thesaurus/TimezoneData.php';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $file = 'Thesaurus/TimezoneData.php';
        
        Storage::disk('database')->put($file, "<?php\n");
        
        Storage::disk('database')->append($file, "namespace Database\Copy\Thesaurus;\n");
        
        Storage::disk('database')->append($file, "// Данные таблицы thesaurus.timezones");
        Storage::disk('database')->append($file, "class TimezoneData");
        Storage::disk('database')->append($file, "{");
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "public function __invoke(): array");
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "{");
        Storage::disk('database')->append($file, Str::repeat(' ', 8) . "return [");
        
        foreach ($this->getTimezones() as $tz) {
            Storage::disk('database')->append($file, Str::repeat(' ', 12) . "(object) [");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'id' => $tz->id,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'name' => '$tz->name',");
            Storage::disk('database')->append($file, Str::repeat(' ', 12) . "],");
        }
        
        Storage::disk('database')->append($file, Str::repeat(' ', 8) . "];");
        
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "}");
        Storage::disk('database')->append($file, "}\n");
        
        $this->info('Команда выполнена.');
    }
    
    private function getTimezones(): iterable
    {
        yield from Timezone::select('id', 'name')->orderBy('id')->get();
    }
}
