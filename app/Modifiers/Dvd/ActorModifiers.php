<?php

namespace App\Modifiers\Dvd;

use App\Models\Dvd\Actor;
use App\Models\Dvd\FilmActor;
use App\Modifiers\Modifiers;
use Illuminate\Support\Facades\DB;

class ActorModifiers extends Modifiers
{
    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            FilmActor::where('actor_id', $id)->delete();
            Actor::where('id', $id)->delete();
        });
    }
}
