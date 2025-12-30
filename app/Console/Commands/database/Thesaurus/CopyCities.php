<?php

namespace App\Console\Commands\database\Thesaurus;

use App\Services\StorageDisk\Thesaurus\CopyCitiesService;
use Illuminate\Console\Command;

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
    public function handle(CopyCitiesService $service): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $service->copy();
        
        $this->info('Команда выполнена.');
    }
}
