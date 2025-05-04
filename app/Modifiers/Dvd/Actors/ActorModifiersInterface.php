<?php

namespace App\Modifiers\Dvd\Actors;

use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\Models\Dvd\Actor;

interface ActorModifiersInterface
{
    public function save(Actor $actor, ActorDto $dto): void;
    
    public function delete(int $id): void;
}
