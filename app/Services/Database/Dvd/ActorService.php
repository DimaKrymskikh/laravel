<?php

namespace App\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\Models\Dvd\Actor;
use App\Models\Dvd\FilmActor;
use Illuminate\Support\Facades\DB;

class ActorService
{
    public function create(ActorDto $dto): Actor
    {
        $actor = new Actor();
        $actor->first_name = $dto->firstName->name;
        $actor->last_name = $dto->lastName->name;
        $actor->save();
        
        return $actor;
    }
    
    public function update(ActorDto $dto, int $actor_id): Actor
    {
        $actor = Actor::find($actor_id);
        $actor->first_name = $dto->firstName->name;
        $actor->last_name = $dto->lastName->name;
        $actor->save();
        
        return $actor;
    }
    
    public function delete(int $actor_id): void
    {
        DB::transaction(function () use ($actor_id) {
            FilmActor::where('actor_id', $actor_id)->delete();
            Actor::find($actor_id)->delete();
        });
    }
}
