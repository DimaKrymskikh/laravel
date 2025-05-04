<?php

namespace App\Queries\Thesaurus\Timezones;

use App\Models\Thesaurus\Timezone;
use Illuminate\Database\Eloquent\Collection;

final class TimezoneQueries implements TimezoneQueriesInterface
{
    public function getList(string $name): Collection
    {
        return Timezone::select('id', 'name')
                ->where('name', 'ilike', "%$name%")
                ->orderBy('name')
                ->get();
    }
}
