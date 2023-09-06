<?php

namespace App\Console\Commands\database\Dvd;

use App\Models\Dvd\Actor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CopyActors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:dvd.actors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Копирование данных таблицы dvd.actors в файл database/Copy/Dvd/ActorData.php';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $file = 'Dvd/ActorData.php';
        
        Storage::disk('database')->put($file, "<?php\n");
        
        Storage::disk('database')->append($file, "namespace Database\Copy\Dvd;\n");
        
        Storage::disk('database')->append($file, "// Данные таблицы dvd.actors");
        Storage::disk('database')->append($file, "class ActorData");
        Storage::disk('database')->append($file, "{");
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "public function __invoke(): array");
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "{");
        Storage::disk('database')->append($file, Str::repeat(' ', 8) . "return [");
        
        foreach ($this->getActors() as $actor) {
            Storage::disk('database')->append($file, Str::repeat(' ', 12) . "(object) [");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'id' => $actor->id,");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'first_name' => '$actor->first_name',");
            Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'last_name' => '$actor->last_name',");
            Storage::disk('database')->append($file, Str::repeat(' ', 12) . "],");
        }
        
        Storage::disk('database')->append($file, Str::repeat(' ', 8) . "];");
        
        Storage::disk('database')->append($file, Str::repeat(' ', 4) . "}");
        Storage::disk('database')->append($file, "}\n");
        
        $this->info('Команда выполнена.');
    }
    
    private function getActors(): iterable
    {
        yield from Actor::select('id', 'first_name', 'last_name')->orderBy('id')->get();
    }
}
