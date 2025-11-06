<?php

namespace App\Modifiers\Dvd\FilmsActors;

use App\Services\Database\Dvd\Dto\FilmActorDto;

interface FilmActorModifiersInterface
{
    public function save(FilmActorDto $dto): void;
    
    public function remove(FilmActorDto $dto): void;
}
