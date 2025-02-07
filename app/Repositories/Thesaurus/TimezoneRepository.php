<?php

namespace App\Repositories\Thesaurus;

use App\Models\Thesaurus\Timezone;
use Illuminate\Database\Eloquent\Collection;

final class TimezoneRepository implements TimezoneRepositoryInterface
{
    public function getList(string $name): Collection
    {
        return Timezone::select('id', 'name')
                ->where('name', 'ilike', "%$name%")
                ->orderBy('name')->get();
    }
}
