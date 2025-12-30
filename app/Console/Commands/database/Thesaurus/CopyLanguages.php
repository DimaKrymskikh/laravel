<?php

namespace App\Console\Commands\database\Thesaurus;

use App\Services\StorageDisk\Thesaurus\CopyLanguagesService;
use Illuminate\Console\Command;

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
    public function handle(CopyLanguagesService $service): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $service->copy();
        
        $this->info('Команда выполнена.');
    }
}
