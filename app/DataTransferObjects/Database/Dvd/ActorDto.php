<?php

namespace App\DataTransferObjects\Database\Dvd;

use App\ValueObjects\PersonName;

final readonly class ActorDto
{
    public function __construct(
        public PersonName $firstName,
        public PersonName $lastName,
    ) {
    }
}
