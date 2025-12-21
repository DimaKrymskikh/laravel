<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

abstract class DBqueries implements DBqueriesInterface
{
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getArray(string $query, array $options = []): array
    {
        return DB::select($query, $options);
    }
}
