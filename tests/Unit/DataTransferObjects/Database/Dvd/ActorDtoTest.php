<?php

namespace Tests\Unit\DataTransferObjects\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\ValueObjects\PersonName;

class ActorDtoTest extends \PHPUnit\Framework\TestCase
{
    public function test_actor_dto_saves_data(): void
    {
        $actor = new ActorDto(
                PersonName::create('Иван', 'first_name', 'attr.actor.first_name.capital_first_letter'),
                PersonName::create('Петров', 'last_name', 'attr.actor.last_name.capital_first_letter'),
            );
        
        $this->assertEquals('Иван', $actor->firstName->name);
        $this->assertEquals('Петров', $actor->lastName->name);
    }
    
    public function test_actor_dto_cannot_be_changed(): void
    {
        $actor = new ActorDto(
                PersonName::create('Иван', 'first_name', 'attr.actor.first_name.capital_first_letter'),
                PersonName::create('Петров', 'last_name', 'attr.actor.last_name.capital_first_letter'),
            );
        
        $newName = PersonName::create('Олег', 'first_name', 'attr.actor.first_name.capital_first_letter');

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Cannot modify readonly property App\DataTransferObjects\Database\Dvd\ActorDto::$firstName');
        $actor->firstName = $newName;

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Cannot modify readonly property App\DataTransferObjects\Database\Dvd\ActorDto::$lastName');
        $actor->lastName = $newName;
    }
}
