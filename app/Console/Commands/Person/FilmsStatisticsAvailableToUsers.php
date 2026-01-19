<?php

namespace App\Console\Commands\Person;

use App\CommandHandlers\Database\Person\FilmsStatisticsAvailableToUsersCommandHandler;
use App\ValueObjects\Date\TimeStringFromSeconds;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FilmsStatisticsAvailableToUsers extends Command
{
    /**
     * Если нужно записать данные в файл: statistics:filmsAndUsers --isFile
     * Если нужно записать данные в консоль: statistics:filmsAndUsers
     *
     * @var string
     */
    protected $signature = 'statistics:filmsAndUsers {--isFile}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Статистика фильмов в наличие у пользователей';

    /**
     * Execute the console command.
     */
    public function handle(FilmsStatisticsAvailableToUsersCommandHandler $handler): void
    {
        $this->info('Старт.');
        $start = microtime(true);
        
        $handler->handle($this);
        
        $this->newLine();
        $this->line('Время выполнения: '.TimeStringFromSeconds::create(microtime(true) - $start)->value);
        $this->info('Команда выполнена.');
    }
    
    /**
     * Записывает данные в консоль.
     * 
     * @param array $films
     * @return void
     */
    public function writeConsole(array $films): void
    {
        $this->line("$this->description\n");
        
        foreach ($films as $film) {
            $this->line("$film->login [$film->id] имеет в своём списке $film->n фильмов.");
        }
    }
    
    /**
     * Записывает данные в файл.
     * 
     * @param array $films
     * @param string $file
     * @return void
     */
    public function writeFile(array $films, string $file): void
    {
        Storage::disk('local')->put($file, "$this->description\n");
        
        foreach ($films as $film) {
            Storage::append($file, "$film->login [$film->id] имеет в своём списке $film->n фильмов.");
        }
    }
}
