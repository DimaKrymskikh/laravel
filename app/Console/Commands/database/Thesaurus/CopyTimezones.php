<?php

namespace App\Console\Commands\database\Thesaurus;

use App\Services\StorageDisk\Thesaurus\CopyTimezonesService;
use Illuminate\Console\Command;

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
    public function handle(CopyTimezonesService $service): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $service->copy();
        
        $this->info('Команда выполнена.');
    }
}
