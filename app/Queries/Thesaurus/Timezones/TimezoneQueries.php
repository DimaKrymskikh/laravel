<?php

namespace App\Queries\Thesaurus\Timezones;

use App\Models\Thesaurus\Timezone;
use App\Support\Collections\Thesaurus\TimezoneCollection;

final class TimezoneQueries implements TimezoneQueriesInterface
{
    public function getList(string $name): TimezoneCollection
    {
        return Timezone::select('id', 'name')
                ->where('name', 'ilike', "%$name%")
                ->orderBy('name')
                ->get();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getListInLazyById(\Closure $callback): void
    {
        Timezone::select('id', 'name')->orderBy('id')
            ->lazyById(self::NUMBER_OF_ITEMS_IN_CHUNCK, column: 'id')
            ->each($callback);
    }
}
