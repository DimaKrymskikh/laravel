<?php

namespace App\Console\Commands\database\Thesaurus;

use App\Models\Thesaurus\Language;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CopyLanguages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:thesaurus.languages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Копирование данных таблицы thesaurus.languages в файл database/Copy/Thesaurus/LanguageData.php';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $file = 'Thesaurus/LanguageData.php';
        
        Storage::disk('database')->put($file, "<?php\n");
        
        Storage::disk('database')->append($file, "namespace Database\Copy\Thesaurus;\n");
        
        Storage::disk('database')->append($file, "// Данные таблицы thesaurus.languages");
        Storage::disk('database')->append($file, "class LanguageData");
        Storage::disk('database')->append($file, "{");
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "public function __invoke(): array");
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "{");
        Storage::disk('database')->append($file, Str::repeat(' ', 8) . "return [");
        
        foreach ($this->getLanguages() as $language) {
            Storage::disk('database')->append($file, Str::repeat(' ', 12) . "(object) [");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'id' => $language->id,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'name' => '$language->name',");
            Storage::disk('database')->append($file, Str::repeat(' ', 12) . "],");
        }
        
        Storage::disk('database')->append($file, Str::repeat(' ', 8) . "];");
        
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "}");
        Storage::disk('database')->append($file, "}\n");
        
        $this->info('Команда выполнена.');
    }
    
    private function getLanguages(): iterable
    {
        yield from Language::select('id', 'name')->orderBy('id')->get();
    }
}
