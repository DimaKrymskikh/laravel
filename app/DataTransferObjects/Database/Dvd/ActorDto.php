<?php

namespace App\DataTransferObjects\Database\Dvd;

use App\ValueObjects\PersonName;

readonly final class ActorDto
{
    public function __construct(
        public PersonName $firstName,
        public PersonName $lastName,
    ) 
    {}
}
