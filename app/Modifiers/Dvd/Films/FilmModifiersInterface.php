<?php

namespace App\Modifiers\Dvd\Films;

use App\Modifiers\ModifiersInterface;

interface FilmModifiersInterface extends ModifiersInterface
{
    public function delete(int $filmId): void;
}
