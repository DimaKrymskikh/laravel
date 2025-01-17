<?php

namespace App\Console\Commands\Dvd\Actor;

use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\Services\Database\Dvd\ActorService;
use App\ValueObjects\PersonName;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

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
     * Execute the console command.
     * 
     * @param ActorService $actorService
     * @return void
     */
    public function handle(ActorService $actorService): void
    {
        $this->info('Старт.');
        
        $this->line("$this->description\n");
        
        try {
            $firstName = $this->ask('Введите имя актёра?');
            $lastName = $this->ask('Введите фамилию актёра?');

            $actorDto = new ActorDto(
                    PersonName::create($firstName, 'first_name', 'attr.actor.first_name.capital_first_letter'),
                    PersonName::create($lastName, 'last_name', 'attr.actor.last_name.capital_first_letter'),
                );
            
            $actor = $actorService->create($actorDto);
            $this->line("В таблицу dvd.actors добавлен актёр $actor->first_name $actor->last_name.");
            $this->info('Команда выполнена.');
            
        } catch(ValidationException $ex) {
            $this->error($ex->getMessage());
        }
    }
}
