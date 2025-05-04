<?php

namespace App\Queries\Thesaurus\Timezones;

use Illuminate\Database\Eloquent\Collection;

interface TimezoneQueriesInterface
{
    public function getList(string $name): Collection;
}
