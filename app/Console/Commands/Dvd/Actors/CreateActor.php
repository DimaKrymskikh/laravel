<?php

namespace App\Console\Commands\Dvd\Actors;

use App\CommandHandlers\Database\Dvd\Actors\CreateActor\CreateActorCommandHandler;
use Illuminate\Console\Command;

class CreateActor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:actor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавляет нового актёра в таблицу dvd.actors';

    /**
     * Выполняет консольную команду.
     * 
     * @param CreateActorCommandHandler $handler
     * @return void
     */
    public function handle(CreateActorCommandHandler $handler): void
    {
        $this->info('Старт.');
        
        $this->line("$this->description\n");
        
        $handler->handle($this);
    }
}
