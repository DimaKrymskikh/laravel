<?php

namespace App\Modifiers\Dvd\Actors;

use App\Modifiers\ModifiersInterface;

interface ActorModifiersInterface extends ModifiersInterface
{
    public function delete(int $id): void;
}
