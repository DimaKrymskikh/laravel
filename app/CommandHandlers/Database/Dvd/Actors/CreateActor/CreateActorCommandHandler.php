<?php

namespace App\CommandHandlers\Database\Dvd\Actors\CreateActor;

use App\Console\Commands\Dvd\Actors\CreateActor;
use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\Exceptions\RuleException;
use App\Services\Database\Dvd\ActorService;
use App\ValueObjects\PersonName;

final class CreateActorCommandHandler
{
    public function __construct(
            private ActorService $actorService
    ) {
    }
    
    /**
     * Выполнение логики команды 'create:actor'
     * 
     * @param CreateActor $command
     * @return void
     */
    public function handle(CreateActor $command): void
    {
        $firstName = $command->ask('Введите имя актёра?');
        $lastName = $command->ask('Введите фамилию актёра?');

        try {
            $actorDto = new ActorDto(
                    PersonName::create($firstName, 'first_name', 'Имя актёра должно начинаться с заглавной буквы. Остальные буквы должны быть строчными.'),
                    PersonName::create($lastName, 'last_name', 'Фамилия актёра должна начинаться с заглавной буквы. Остальные буквы должны быть строчными.'),
                );
            
            $actor = $this->actorService->create($actorDto);
            $command->line("В таблицу dvd.actors добавлен актёр $actor->full_name.");
            $command->info('Команда выполнена.');
            
        } catch(RuleException $ex) {
            $command->error($ex->getMessage());
            $command->info('Команда прервана.');
        }
    }
}
