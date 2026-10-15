<?php

namespace App\Queries;

use App\Contracts\Database\DBqueriesInterface;
use Illuminate\Support\Facades\DB;

/**
 * Класс содержит оболочки над DB::select, DB::scalar.
 */
class DBqueries implements DBqueriesInterface
{
    /**
     * {@inheritDoc}
     */
    public function getArray(string $query, array $options = []): array
    {
        return DB::select($query, $options);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getObject(string $query, array $options = []): object
    {
        $arr = $this->getArray($query, $options);
        
        return array_shift($arr) ?? (object) [];
    }
    
    /**
     * {@inheritDoc}
     */
    public function getValue(string $query, array $options = []): mixed
    {
        return DB::scalar($query, $options);
    }
}
