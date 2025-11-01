<?php

namespace App\Services\Quiz;

use Illuminate\Support\Collection;

interface StatusInterface
{
    public function getNextStatuses(): Collection;
}
