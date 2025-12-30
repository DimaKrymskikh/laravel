<?php

namespace App\Console\Commands\database\Dvd;

use App\Services\StorageDisk\Dvd\CopyActorsService;
use Illuminate\Console\Command;

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
    public function handle(CopyActorsService $service): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $service->copy();
        
        $this->info('Команда выполнена.');
    }
}
