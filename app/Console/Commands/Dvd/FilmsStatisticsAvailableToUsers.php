<?php

namespace App\Console\Commands\Dvd;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FilmsStatisticsAvailableToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:filmsAndUsers {isFile?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Статистика фильмов в наличие у пользователей';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Старт.');
        
        $films = $this->getStatistics();
        
        if ($this->argument('isFile')) {
            $this->writeFile($films, "commannds\Dvd\FilmsStatisticsAvailableToUsers_" . date('d-m-Y') . ".txt");
        } else {
            $this->writeConsole($films);
        }
        
        $this->info('Команда выполнена.');
    }
    
    private function getStatistics(): Collection
    {
        return User::leftJoin('person.users_films', 'person.users_films.user_id', '=', 'person.users.id')
                ->select('person.users.id', 'person.users.login', DB::raw('count(person.users_films.film_id) as n'))
                ->groupBy('person.users.id')
                ->orderBy('n', 'desc')
                ->orderBy('person.users.login')
                ->get();
    }
    
    private function writeConsole(Collection $films): void
    {
        $this->line("$this->description\n");
        
        foreach ($films as $film) {
            $this->line("$film->login [$film->id] имеет в своём списке $film->n фильмов.");
        }
    }
    
    private function writeFile(Collection $films, string $file): void
    {
        Storage::disk('local')->put($file, "$this->description\n");
        
        foreach ($films as $film) {
            Storage::append($file, "$film->login [$film->id] имеет в своём списке $film->n фильмов.");
        }
    }
}
