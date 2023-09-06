<?php

namespace App\Console\Commands\database\Dvd;

use App\Models\Dvd\Film;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CopyFilms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:dvd.films';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Копирование данных таблицы dvd.films в файл database/Copy/Dvd/FilmData.php';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $file = 'Dvd/FilmData.php';
        
        Storage::disk('database')->put($file, "<?php\n");
        
        Storage::disk('database')->append($file, "namespace Database\Copy\Dvd;\n");
        
        Storage::disk('database')->append($file, "// Данные таблицы dvd.films");
        Storage::disk('database')->append($file, "class FilmData");
        Storage::disk('database')->append($file, "{");
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "public function __invoke(): array");
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "{");
        Storage::disk('database')->append($file, Str::repeat(' ', 8) . "return [");
        
        foreach ($this->getFilms() as $film) {
            Storage::disk('database')->append($file, Str::repeat(' ', 12) . "(object) [");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'id' => $film->id,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'title' => '$film->title',");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'description' => '$film->description',");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'release_year' => $film->release_year,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'language_id' => $film->language_id,");
            Storage::disk('database')->append($file, Str::repeat(' ', 12) . "],");
        }
        
        Storage::disk('database')->append($file, Str::repeat(' ', 8) . "];");
        
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "}");
        Storage::disk('database')->append($file, "}\n");
        
        $this->info('Команда выполнена.');
    }
    
    private function getFilms(): iterable
    {
        yield from Film::select('id', 'title', 'description', 'release_year', 'language_id')->orderBy('id')->get();
    }
}
