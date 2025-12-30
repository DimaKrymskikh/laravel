<?php

namespace App\Console\Commands\database\Dvd;

use App\Services\StorageDisk\Dvd\CopyFilmsActorsService;
use Illuminate\Console\Command;

class CopyFilmsActors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:dvd.films_actors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Копирование данных таблицы dvd.films_actors в файл database/Copy/Dvd/FilmActorData.php';

    /**
     * Execute the console command.
     */
    public function handle(CopyFilmsActorsService $service): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $service->copy();
        
        $this->info('Команда выполнена.');
    }
}
