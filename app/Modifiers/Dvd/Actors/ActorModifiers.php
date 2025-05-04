<?php

namespace App\Modifiers\Dvd\Actors;

use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\Models\Dvd\Actor;
use App\Models\Dvd\FilmActor;
use Illuminate\Support\Facades\DB;

final class ActorModifiers implements ActorModifiersInterface
{
    public function save(Actor $actor, ActorDto $dto): void
    {
        $actor->first_name = $dto->firstName->name;
        $actor->last_name = $dto->lastName->name;
        $actor->save();
    }
    
    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            FilmActor::where('actor_id', $id)->delete();
            Actor::where('id', $id)->delete();
        });
    }
}
