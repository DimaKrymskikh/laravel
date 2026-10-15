<?php

namespace App\Queries\Person;

use App\Models\User;
use App\Queries\ModelQueries;

class UserQueries extends ModelQueries
{
    protected function setClassName(): void
    {
        $this->className = User::class;
    }
}
