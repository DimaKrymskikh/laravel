<?php

namespace App\Repositories\Thesaurus;

use Illuminate\Database\Eloquent\Collection;

interface TimezoneRepositoryInterface
{
    public function getList(string $name): Collection;
}
