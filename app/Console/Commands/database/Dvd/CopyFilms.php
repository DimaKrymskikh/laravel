<?php

namespace App\Console\Commands\database\Dvd;

use App\Services\StorageDisk\Dvd\CopyFilmsService;
use Illuminate\Console\Command;

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
    public function handle(CopyFilmsService $service): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $service->copy();
        
        $this->info('Команда выполнена.');
    }
}
